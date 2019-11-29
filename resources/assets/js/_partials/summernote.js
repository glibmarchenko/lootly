
import summernote from 'summernote';

Vue.component('summer-note', {
    template: '<textarea :name="name"></textarea>',
    props: {
        value: {
            type: String,
        },
        name: {
            type: String,
            required: true,
        },
        height: {
            type: String,
            default: '250'
        },
        placeholder: {
            type: String,
            default: ''
        }
    },
    mounted() {
        const config = {
            height: this.height,
            placeholder: this.placeholder,
        };

        config.callbacks = {
            onInit: () => $(this.$el).summernote("code", this.value),
            onChange: () => this.$emit('change', $(this.$el).summernote('code')),
            onBlur: () => this.$emit('change', $(this.$el).summernote('code')),
        };

        $(this.$el).summernote(config);
    },
});
