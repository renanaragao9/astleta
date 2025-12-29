export interface CompanyUser {
    name: string;
    email: string;
}

export interface CompanyContact {
    type: {
        name: string | null;
        icon: string | null;
    };
    value: string;
}

export interface CompanyDocumentType {
    id: number;
    name: string;
}

export interface CompanyDocument {
    number: string;
    type: CompanyDocumentType;
    filePath: string | null;
    status: string;
    description: string | null;
}

export interface CompanyAddress {
    zipcode: string;
    country: string;
    state: string;
    city: string;
    district: string;
    street: string;
    number: string;
    complement?: string | null;
    latitude?: number | null;
    longitude?: number | null;
}

export interface MonthlyRealizedTotals {
    totalRevenue: number;
    totalExpenses: number;
    totalBalance: number;
    totalTransferFees: number;
}

export interface Company {
    id: number;
    name: string;
    cnpj: string;
    cpf?: string;
    phone: string;
    description: string;
    imagePath: string;
    status: string;
    user: CompanyUser;
    contacts: CompanyContact[];
    documents: CompanyDocument[];
    address: CompanyAddress | null;
    createdAt: string;
    updatedAt: string;
    monthlyRealizedTotals: MonthlyRealizedTotals;
}

export interface CompanyResponse {
    status: string;
    message: string;
    data: Company;
}
