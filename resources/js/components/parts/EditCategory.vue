<template>
    <div class="modal fade" id="edit-category" ref="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Category - {{ this.category_name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form ref="editForm" @submit.prevent.stop="onSubmit()">
                    <div class="modal-body">
                        <input type="hidden" name="category_id" :value="category_id">
                        <div class="form-group">
                            <label for="category-name">Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="category_name" :value="category_name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'edit-category',
        props: ['category_id', 'category_name'],
        data(){
            return {
                
            }
        },
        methods:{
            onSubmit(){
                let formData = new FormData(this.$refs.editForm);

                axios.post(this.$parent.edit_category_action, formData)
                    .then(response => {
                        if(response.data.message == "success"){
                            this.$parent.foodAndCategories = response.data.payload;
                            $(this.$refs.editMenuModal).modal("hide");
                            this.$parent.getFoodCategoryList();
                        }
                        this.loading = false;
                    }).catch(error => {
                        if (error.response && error.response.status == 422){
                            this.validationErrors = error.response.data.errors;
                        }
                        this.loading = false;
                    });
            }
        }
    }
</script>
