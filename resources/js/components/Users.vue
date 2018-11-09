<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Responsive Hover Table</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal()">
                                Add New
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>create</th>
                                <th>Modify</th>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.id}}</td>
                                <td>{{ user.name | upText}}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.type | upText}}</td>
                                <td>{{ user.created_at | myDate}}</td>
                                <td>
                                    <a href="javascript:;" @click="editModal(user)">
                                        <i class="nav-icon fas fa-edit green"></i>
                                    </a>
                                    /
                                    <a href="javascript:;" @click="deleteUser(user.id)">
                                        <i class="nav-icon fas fa-trash red"></i>
                                    </a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title text-center" id="addNewCenterTitle">Add New</h5>
                        <h5 v-show="editMode" class="modal-title text-center">Editar User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateUser() :createUser()" id="formModalUser">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input v-model="form.name" type="text" name="name"
                                               placeholder="Nombre completo"
                                               class="form-control" :class="{ 'is-invalid' : form.errors.has('name')} "/>
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <input v-model="form.email" type="email" name="email"
                                               placeholder="Correo"
                                               class="form-control" :class="{ 'is-invalid' : form.errors.has('email')} "/>
                                        <has-error :form="form" field="email"></has-error>
                                    </div>
                                    <div class="form-group">
                                    <textarea v-model="form.bio" name="bio"
                                              placeholder="Biografia corta del usuario(Opcional)."
                                              class="form-control" :class="{ 'is-invalid' : form.errors.has('bio')}"></textarea>
                                        <has-error :form="form" field="bio"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <select v-model="form.type" name="type"
                                                class="form-control" :class="{ 'is-invalid' : form.errors.has('type')}">
                                            <option value="">Seleccione un rol</option>
                                            <option value="admin">Administrador</option>
                                            <option value="user">Usuario</option>
                                            <option value="author">Autor</option>
                                        </select>
                                        <has-error :form="form" field="type"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <input v-model="form.password" type="password" name="password"
                                               placeholder="Contraseña"
                                               class="form-control" :class="{ 'is-invalid' : form.errors.has('password')} "/>
                                        <has-error :form="form" field="password"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
                            <button v-show="editMode" type="submit" class="btn btn-primary">edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Users",
        data() {
            return {
                users: {},
                editMode: false,
                form: new Form({
                    id: "",
                    name: "",
                    email: "",
                    password: "",
                    type: "",
                    bio: "",
                    photo: ""
                }),
            }
        },
        methods: {
            newModal() {
                this.editMode = false;
                this.form.reset();
                $("#addNew").modal("show");
            },
            editModal(user) {
                this.editMode = true;
                this.form.reset();
                this.form.fill(user);
                $("#addNew").modal("show");
            },
            loadUsers() {
                axios.get('api/user')
                    .then(({data}) => {
                        this.users = data.data;
                    })
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                    .then(() => {
                        Fire.$emit('afterCreate');
                        //this.loadUsers();
                        $("#addNew").modal("hide");
                        toast({
                            type: 'success',
                            title: 'Signed in successfully'
                        });

                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },
            deleteUser(user_id) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    this.$Progress.start();
                    if (result.value) {
                        this.form.delete(`api/user/${user_id}`, {
                            id: user_id
                        })
                            .then((response) => {
                                Fire.$emit('afterCreate');
                                swal(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                );
                                this.$Progress.finish();
                            })
                            .catch(e => {
                                this.$Progress.fail();
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                })
                            })
                    } else {
                        this.$Progress.fail();
                    }
                })
            },
            updateUser() {
                this.$Progress.start();
                this.form.put(`api/user/${this.form.id}`)
                    .then(() => {
                       Fire.$emit('afterCreate');
                        $("#addNew").modal("hide");
                        toast({
                            type: 'success',
                            title: 'Signed in successfully'
                        });

                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            }
        },
        created() {
            this.loadUsers();
            Fire.$on('afterCreate', () => {
                this.loadUsers();
            });
            //setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>

<style scoped>

</style>
