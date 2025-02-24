<template>
    <div class="mb-3">
        <label class="form-label">{{ label }}</label>
        <input v-bind="$attrs" :value="modelValue" :type="type" :class="{ 'is-invalid': error }" class="form-control"
            @input="$emit('update:modelValue', $event.target.value)" />
        <p class="invalid-feedback text-xs mb-0" v-if="error">{{ error }}</p>
    </div>
</template>

<script setup>
import { toRefs, useAttrs } from "vue";

const props = defineProps({
    modelValue: String,
    label: String,
    type: { type: String, default: "text" },
    error: String
});

const attrs = useAttrs();
const emit = defineEmits(["update:modelValue"]);
const { modelValue, label, type, error } = toRefs(props);
</script>