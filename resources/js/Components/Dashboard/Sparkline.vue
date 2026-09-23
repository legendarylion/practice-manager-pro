<script setup>
import { computed } from 'vue';

const props = defineProps({
    values: { type: Array, required: true },
    height: { type: Number, default: 40 },
    width: { type: Number, default: 140 },
    stroke: { type: String, default: '#0ea5e9' },
    fill: { type: String, default: 'rgba(14,165,233,0.08)' },
});

const path = computed(() => {
    const vals = props.values.filter(v => typeof v === 'number');
    if (vals.length < 2) return null;
    const min = Math.min(...vals);
    const max = Math.max(...vals);
    const range = max - min || 1;
    const stepX = props.width / (vals.length - 1);
    const points = vals.map((v, i) => {
        const x = i * stepX;
        const y = props.height - ((v - min) / range) * (props.height - 4) - 2;
        return [x, y];
    });
    const line = points.map(([x, y], i) => `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`).join(' ');
    const area = `${line} L${props.width},${props.height} L0,${props.height} Z`;
    return { line, area };
});
</script>

<template>
    <svg v-if="path" :viewBox="`0 0 ${width} ${height}`" :width="width" :height="height" class="block">
        <path :d="path.area" :fill="fill" stroke="none" />
        <path :d="path.line" :stroke="stroke" stroke-width="1.75" fill="none" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <div v-else class="text-[10px] text-gray-400">Not enough data</div>
</template>
