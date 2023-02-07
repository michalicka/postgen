<template>
    <div>
        <label class="form-label">{{ __('Rating') }}:</label>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-light" :class="{ 'active': type === 'like' }" @click="rate('like')">
                <like style="width:20px; height:20px;" />
            </button>
            <button type="button" class="btn btn-light" :class="{ 'active': type === 'dislike' }" @click="rate('dislike')">
                <dislike style="width:20px; height:20px;" />
            </button>
        </div>
    </div>
</template>

<script>
import Like from '../../UI/vue/Icons/like';
import Dislike from '../../UI/vue/Icons/dislike';

export default {
    components: { Like, Dislike },
    props: {
        value: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            id: this.value,
            type: null
        }
    },
    methods: {
        rate(type) {
            axios.post('/rate', {
                id: this.id,
                type: type,
            }).then(({data}) => {
                this.type = data.type;
            });
        }
    },
    mounted() {
    }
}
</script>
