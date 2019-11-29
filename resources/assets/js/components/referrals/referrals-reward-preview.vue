<template>
    <div class="col-md-5 col-12">
        <div class="sticky-top preview-box">
            <div class="well">
                <div :class="{ 'loading' : loading }" v-cloak>
                    <h5 class="bold m-b-15">Reward Preview</h5>
                    <div class="preview-action">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="left-preview-icon icon_el"></label>
                                <div class="right-preview-text">
                                    <span>
                                        <h5 class="bold m-b-0 break-words" v-text="form.program.name"></h5>
                                        <span class="break-words" v-text="form.program.rewardText"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="light-font m-t-5 m-l-25">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script type="text/javascript">
    export default {
        props:{
            form:{
                required: true
            },
            loading:{
                required: true
            },
            reward_text:{
                required: false
            },
        } ,


    }
</script>

