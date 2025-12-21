<template>
    <component :is="componentType" v-bind="componentProps" :class="classes">
        <slot />
    </component>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    to: {
        type: [String, Object],
    },
    as: {
        type: String,
        default: 'link',
    },
});

const componentType = computed(() => {
    return props.as === 'button' ? 'button' : 'router-link';
});

const componentProps = computed(() => {
    if (props.as === 'button') {
        return { type: 'button' };
    }
    return { to: props.to };
});

const classes = 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800';
</script>
