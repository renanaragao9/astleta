/**
 * Utilitários de validação para verificação de email
 */

/**
 * Valida se o email tem formato válido
 */
export const isValidEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};
