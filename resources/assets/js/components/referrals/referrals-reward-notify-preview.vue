<template>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
        <tbody>
            <tr>
                <td class="img" style="padding: 0 10px; text-align:left;">
                    <div class="text-center m-b-20">
                        <img :src="logo" style="max-height: 50px;">
                    </div>
                    <div v-html="body_text"></div>
                </td>
            </tr>
            <!-- END Button -->
            <div class="border-top p-l-15 p-r-15" style="margin-top: 15px;"><p class="text-center m-t-10">Powered by Lootly</p></div>
        </tbody>
    </table>
</template>


<script type="text/javascript">
    export default {
        props:{
            body_text:{
                required: true
            },
            logo: {
                required: true
            },
        } ,


    }
</script>

