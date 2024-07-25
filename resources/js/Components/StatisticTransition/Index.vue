<template>
    <div>
        <transition name="fade">
            <div v-if="show">{{ displayNumber }}</div>
        </transition>
    </div>
</template>

<script>

export default {
    name: 'StatisticTransition',
    props: {
        startNumber: {
            type: Number,
            required: true
        },
        endNumber: {
            type: Number,
            required: true
        },
        duration: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            currentNumber: 0,
            show: true,
            displayNumber: 0
        };
    },
    methods: {
        startTransition() {
            const increment = (this.endNumber - this.startNumber) / (this.duration / 100);
            this.currentNumber = this.startNumber;
            const interval = setInterval(() => {
                this.currentNumber += increment;
                this.displayNumber = Math.round(this.currentNumber);
                if (this.currentNumber >= this.endNumber) {
                    clearInterval(interval);
                }
            }, 100);
        }
    },
    mounted() {
        this.startTransition();
    }
}
</script>
