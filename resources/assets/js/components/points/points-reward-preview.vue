<template>
    <div class="col-md-5 col-12">
        <div class="sticky-top preview-box">
            <div class="well">
                <div :class="{ 'loading' : loading }" v-cloak>
                    <h5 class="bold m-b-15">Reward Preview</h5>
                    <div class="preview-action">
                        <div class="row" style="min-height: 39px;"> 
                            <div class="col-md-12">
                                <label :class="'left-preview-icon ' +icon_name"></label>
                                <div class="right-preview-text">
                                    <span>
                                        <h5 class="bold m-b-0" v-text="program_name"></h5>
                                        <p>
                                            <span v-if="reward_points != '' " v-text="reward_text"></span>
                                        </p>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

"form.reward.variable.value != '' && form.reward.variable.points != ''"
<script type="text/javascript">
    export default {
        props: {
            program_name: null,
            reward_points: null,
            reward_text:null,
            icon_name:null,
            loading: true,
        },
    }
</script>

