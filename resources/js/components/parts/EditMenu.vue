<template>
    <div class="modal fade" id="edit-menu" ref="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form v-if="this.$parent.getEditingMenu" @submit.prevent="postForm" id="editMenuForm" method="post" :action="this.$parent.edit_menu_action" enctype="multipart/form-data">
                    <div class="modal-body">
                        <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>
                        <input type="hidden" id="food_id" name="food_id" :value="this.$parent.editing.menu">
                        <div class="form-group">
                            <label for="menu-name">Menu name</label>
                            <input type="text" :value="this.$parent.getEditingMenu.food_name" name="food_name" id="food_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="menu-image">Menu image</label>
                            <input type="file" ref="imageInput" @change="loadFile($event)" name="food_image_url" id="food_image_url" class="form-control-file" accept="image/*">
                            <div class="thumb-container">
                                <img v-if="!outputSrc" id="output" :src="getImageUrl(this.$parent.getEditingMenu.food_image_url)" class="img-thumbnail"/>
                                <img v-else id="output" :src="outputSrc" class="img-thumbnail"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="menu-price">Menu price</label>
                            <input type="text" :value="this.$parent.getEditingMenu.unit_price" name="unit_price" id="unit_price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="menu-description">Description</label>
                            <input type="text" :value="this.$parent.getEditingMenu.description" name="description" id="description" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span><i class="fa fa-spinner fa-spin" v-if="loading"></i></span>
                        <input  type="submit" class="btn btn-primary" value="Save"/>
                        <button type="button" @click="resetForm()" class="btn btn-default" data-dismiss="modal">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'edit-menu',
        props: ['site_url'],
        data(){
            return {
                outputSrc: null,
                validationErrors: '',
                loading: false
            }
        },
        mounted(){
            $(this.$refs.editMenuModal).on("hidden.bs.modal", this.resetForm);
        },
        methods: {
            postForm(){
                let form = document.getElementById('editMenuForm');
                let formData = new FormData(form);
                formData.append('food_image_url', document.querySelector('#food_image_url').files[0]);

                this.loading = true;
                axios.post(this.$parent.edit_menu_action, formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    if(response.data.message == "success"){
                        this.$parent.updateSingleMenu($('#food_id').val(), response.data.payload);
                        $(this.$refs.editMenuModal).modal("hide");
                    }
                    this.loading = false;
                }).catch(error => {
                    if (error.response && error.response.status == 422){
                        this.validationErrors = error.response.data.errors;
                    }
                    this.loading = false;
                });
            },

            loadFile(event){
                this.outputSrc = URL.createObjectURL(event.target.files[0]);
            },

            getImageUrl(img){
                return this.site_url+'/storage/rest_food/'+img;
            },

            resetForm(){
                this.outputSrc = null;
                const input = this.$refs.imageInput;
                input.type = 'text';
                input.type = 'file';
            }
        },
        components: {
            ValidationErrors: require('../ValidationErrors.vue')
        }
    }
</script>

