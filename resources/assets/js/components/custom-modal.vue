<template>
     <b-modal class="custom-modal" @hidden="onHidden"  hide-footer :id="id" :title="title" v-cloak>
         <form role="form">
            <div class="row">
                <div class="col-md-12">
                   <slot></slot>
                </div>
            </div>
            <footer class="row m-t-10 p-b-20 p-t-20 modal-footer">
                <div class="col-md-6 offset-md-3" >
                    <button @click.prevent="hideModal" class="btn btn-block">
                     Close
                    </button>
                </div>
            </footer>
        </form>
     </b-modal>
</template>

<script>
    export default {
         props: {
             title: {
                default: '',
                type: String
            },
            close_callback: {
                default: () =>{},
                type: Function
            },
            toggle_modal: {
                default: false,
                type: Boolean
            },
            id: {
                default: 'modal',
                type: String
            },
        },
        data: function () {
            return {
            }
        },
         created: function () {
        },
        methods: {
            onHidden: function(){
                this.close_callback();
            },
            hideModal: function(){
                this.$root.$emit('bv::hide::modal', this.id);
             },
        },

        watch: {
             toggle_modal: function(val) {
                if(val) {
                    return this.$root.$emit('bv::show::modal', this.id)
                } else {
                    return this.hideModal();
                }
            },
        }
    }
</script>
<style>
.custom-modal footer{
    background: #f0f2f7;
    margin: -14px;
    justify-content: flex-start;
}
</style>
