/**
 * Formata uma data para string YYYY-MM-DD no fuso horário local
 * @param date - A data a ser formatada
 * @returns String no formato YYYY-MM-DD
 */
export const formatLocalDate = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

/**
 * Converte uma string de data YYYY-MM-DD para Date no fuso horário local
 * @param dateString - String no formato YYYY-MM-DD
 * @returns Date object
 */
export const parseLocalDate = (dateString: string): Date => {
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
};
