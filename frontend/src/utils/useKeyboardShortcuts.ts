import { onMounted, onUnmounted, type Ref } from 'vue';

export function useKeyboardShortcuts(onF1: () => void, onEnter: () => void, enterCondition?: Ref<boolean> | (() => boolean), onF4?: () => void) {
    const handleKeydown = (event: KeyboardEvent) => {
        if (event.key === 'F1') {
            event.preventDefault();
            onF1();
        } else if (event.key === 'Enter') {
            const condition = typeof enterCondition === 'function' ? enterCondition() : enterCondition?.value;
            if (condition) {
                event.preventDefault();
                onEnter();
            }
        } else if (event.key === 'F4' && onF4) {
            event.preventDefault();
            onF4();
        }
    };

    onMounted(() => {
        window.addEventListener('keydown', handleKeydown);
    });

    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeydown);
    });
}
