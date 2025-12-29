<script setup lang="ts">
import { ref, onMounted } from 'vue';

const display = ref('0');
let currentInput = '';
let operator = '';
let previousInput = '';
let expression = '0';
const calculatorRef = ref<HTMLDivElement>();

onMounted(() => {
    if (calculatorRef.value) {
        calculatorRef.value.focus();
    }
});

function appendNumber(num: string) {
    if (operator === '' && previousInput !== '' && currentInput === '') {
        expression = num;
        previousInput = '';
        display.value = expression;
    } else {
        if (expression === '0') {
            expression = num;
        } else {
            expression += num;
        }
        display.value = expression;
    }
    currentInput += num;
}

function appendOperator(op: string) {
    if (currentInput !== '') {
        if (previousInput !== '') {
            calculate();
        }
        previousInput = currentInput;
        currentInput = '';
        operator = op;
        expression = previousInput + op;
        display.value = expression;
    }
}

function calculate() {
    if (previousInput !== '' && currentInput !== '' && operator !== '') {
        const prev = parseFloat(previousInput);
        const curr = parseFloat(currentInput);
        let result = 0;
        switch (operator) {
            case '+':
                result = prev + curr;
                break;
            case '-':
                result = prev - curr;
                break;
            case '*':
                result = prev * curr;
                break;
            case '/':
                result = prev / curr;
                break;
        }
        expression = previousInput + operator + currentInput + '=' + result.toString();
        display.value = expression;
        previousInput = result.toString();
        currentInput = '';
        operator = '';
    }
}

function clear() {
    display.value = '0';
    currentInput = '';
    operator = '';
    previousInput = '';
    expression = '0';
}

function handleKeyDown(event: KeyboardEvent) {
    const key = event.key;
    if (key >= '0' && key <= '9') {
        appendNumber(key);
    } else if (key === '.') {
        appendNumber('.');
    } else if (key === '+' || key === '-' || key === '*' || key === '/') {
        appendOperator(key);
    } else if (key === 'Enter' || key === '=') {
        calculate();
    } else if (key === 'Escape' || key === 'c' || key === 'C') {
        clear();
    }
    event.preventDefault();
}
</script>

<template>
    <div class="calculator bg-gray-100 p-6 rounded-lg shadow-md max-w-sm mx-auto" @keydown="handleKeyDown" tabindex="0" ref="calculatorRef">
        <div class="display bg-white p-3 mb-6 rounded text-right text-2xl font-mono">
            {{ display }}
        </div>
        <div class="grid grid-cols-4 gap-3">
            <button @click="clear" class="col-span-2 bg-red-500 text-white p-3 rounded hover:bg-red-600 text-lg">C</button>
            <button @click="appendOperator('/')" class="bg-blue-500 text-white p-3 rounded hover:bg-blue-600 text-lg">/</button>
            <button @click="appendOperator('*')" class="bg-blue-500 text-white p-3 rounded hover:bg-blue-600 text-lg">*</button>

            <button @click="appendNumber('7')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">7</button>
            <button @click="appendNumber('8')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">8</button>
            <button @click="appendNumber('9')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">9</button>
            <button @click="appendOperator('-')" class="bg-blue-500 text-white p-3 rounded hover:bg-blue-600 text-lg">-</button>

            <button @click="appendNumber('4')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">4</button>
            <button @click="appendNumber('5')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">5</button>
            <button @click="appendNumber('6')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">6</button>
            <button @click="appendOperator('+')" class="bg-blue-500 text-white p-3 rounded hover:bg-blue-600 text-lg">+</button>

            <button @click="appendNumber('1')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">1</button>
            <button @click="appendNumber('2')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">2</button>
            <button @click="appendNumber('3')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">3</button>
            <button @click="calculate" class="row-span-2 bg-green-500 text-white p-3 rounded hover:bg-green-600 text-lg">=</button>

            <button @click="appendNumber('0')" class="col-span-2 bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">0</button>
            <button @click="appendNumber('.')" class="bg-gray-200 p-3 rounded hover:bg-gray-300 text-lg">.</button>
        </div>
    </div>
</template>

<style scoped>
.calculator {
    font-family: Arial, sans-serif;
}
</style>
