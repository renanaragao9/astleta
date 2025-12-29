export function useFormat() {
    const formatCurrency = (value: number | null | undefined): string => {
        if (value == null || isNaN(value)) return 'R$ 0,00';
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    };

    const formatNumber = (value: number | null | undefined, decimals: number = 2): string => {
        if (value == null || isNaN(value)) return '0';
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(value);
    };

    const formatDate = (value: string | Date | null | undefined): string => {
        if (!value) return '';
        const date = typeof value === 'string' ? new Date(value) : value;
        return new Intl.DateTimeFormat('pt-BR').format(date);
    };

    return {
        formatCurrency,
        formatNumber,
        formatDate
    };
}
