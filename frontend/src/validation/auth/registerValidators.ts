/**
 * Utilitários de validação para registro
 */

export const isValidEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

export const isStrongPassword = (password: string): boolean => {
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSymbols = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    return password.length >= 8 && hasUpperCase && hasLowerCase && hasNumbers && hasSymbols;
};

export const isValidCpf = (cpf: string): boolean => {
    const cleanedCpf = cpf.replace(/[.-]/g, '');

    if (cleanedCpf.length !== 11) return false;

    if (/^(\d)\1{10}$/.test(cleanedCpf)) return false;

    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cleanedCpf.charAt(i)) * (10 - i);
    }
    let remainder = 11 - (sum % 11);
    const digit1 = remainder >= 10 ? 0 : remainder;

    if (digit1 !== parseInt(cleanedCpf.charAt(9))) return false;

    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cleanedCpf.charAt(i)) * (11 - i);
    }
    remainder = 11 - (sum % 11);
    const digit2 = remainder >= 10 ? 0 : remainder;

    return digit2 === parseInt(cleanedCpf.charAt(10));
};

export const cleanPhone = (phone: string): string => {
    return phone.replace(/[^\d]/g, '');
};

export const isValidPhone = (phone: string): boolean => {
    const cleanedPhone = cleanPhone(phone);

    if (cleanedPhone.length === 11) {
        return /^[1-9]{2}9[0-9]{8}$/.test(cleanedPhone);
    } else if (cleanedPhone.length === 10) {
        return /^[1-9]{2}[2-5][0-9]{7}$/.test(cleanedPhone);
    }

    return false;
};

export const isValidName = (name: string): boolean => {
    const trimmedName = name.trim();
    const words = trimmedName.split(/\s+/);
    return words.length >= 2 && words.every((word) => word.length >= 2);
};

export const isValidBirthDate = (date: string): boolean => {
    const birthDate = new Date(date);
    const today = new Date();
    const minAge = 14;

    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        return age - 1 >= minAge;
    }

    return age >= minAge;
};

export const passwordsMatch = (password: string, confirmPassword: string): boolean => {
    return password === confirmPassword;
};
