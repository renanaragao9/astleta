<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->insert(['name' => 'index-user', 'label' => 'Visualizar usuários', 'group' => 'Usuários']);
        DB::table('permissions')->insert(['name' => 'show-user', 'label' => 'Visualizar detalhes do usuário', 'group' => 'Usuários']);
        DB::table('permissions')->insert(['name' => 'create-user', 'label' => 'Adicionar usuários', 'group' => 'Usuários']);
        DB::table('permissions')->insert(['name' => 'edit-user', 'label' => 'Editar usuários', 'group' => 'Usuários']);
        DB::table('permissions')->insert(['name' => 'delete-user', 'label' => 'Remover usuários', 'group' => 'Usuários']);
        DB::table('permissions')->insert(['name' => 'inactive-user', 'label' => 'Inativar usuários', 'group' => 'Usuários']);
        DB::table('permissions')->insert(['name' => 'change-password', 'label' => 'Trocar senha', 'group' => 'Usuários']);

        DB::table('permissions')->insert(['name' => 'index-permission', 'label' => 'Visualizar permissões', 'group' => 'Permissões']);
        DB::table('permissions')->insert(['name' => 'show-permission', 'label' => 'Visualizar detalhes da permissão', 'group' => 'Permissões']);
        DB::table('permissions')->insert(['name' => 'create-permission', 'label' => 'Adicionar permissões', 'group' => 'Permissões']);
        DB::table('permissions')->insert(['name' => 'edit-permission', 'label' => 'Editar permissões', 'group' => 'Permissões']);
        DB::table('permissions')->insert(['name' => 'delete-permission', 'label' => 'Remover permissões', 'group' => 'Permissões']);

        DB::table('permissions')->insert(['name' => 'index-profile', 'label' => 'Visualizar perfis', 'group' => 'Perfis']);
        DB::table('permissions')->insert(['name' => 'show-profile', 'label' => 'Visualizar detalhes do perfil', 'group' => 'Perfis']);
        DB::table('permissions')->insert(['name' => 'create-profile', 'label' => 'Criar perfis', 'group' => 'Perfis']);
        DB::table('permissions')->insert(['name' => 'edit-profile', 'label' => 'Editar perfis', 'group' => 'Perfis']);
        DB::table('permissions')->insert(['name' => 'delete-profile', 'label' => 'Remover perfis', 'group' => 'Perfis']);

        DB::table('permissions')->insert(['name' => 'index-sport', 'label' => 'Visualizar esportes', 'group' => 'Esportes']);
        DB::table('permissions')->insert(['name' => 'show-sport', 'label' => 'Visualizar detalhes do esporte', 'group' => 'Esportes']);
        DB::table('permissions')->insert(['name' => 'create-sport', 'label' => 'Adicionar esportes', 'group' => 'Esportes']);
        DB::table('permissions')->insert(['name' => 'edit-sport', 'label' => 'Editar esportes', 'group' => 'Esportes']);
        DB::table('permissions')->insert(['name' => 'delete-sport', 'label' => 'Remover esportes', 'group' => 'Esportes']);

        // Permissions for Skills
        DB::table('permissions')->insert(['name' => 'index-skill', 'label' => 'Visualizar habilidades', 'group' => 'Habilidades']);
        DB::table('permissions')->insert(['name' => 'show-skill', 'label' => 'Visualizar detalhes da habilidade', 'group' => 'Habilidades']);
        DB::table('permissions')->insert(['name' => 'create-skill', 'label' => 'Adicionar habilidades', 'group' => 'Habilidades']);
        DB::table('permissions')->insert(['name' => 'edit-skill', 'label' => 'Editar habilidades', 'group' => 'Habilidades']);
        DB::table('permissions')->insert(['name' => 'delete-skill', 'label' => 'Remover habilidades', 'group' => 'Habilidades']);

        // Permissions for Positions
        DB::table('permissions')->insert(['name' => 'index-position', 'label' => 'Visualizar posições', 'group' => 'Posições']);
        DB::table('permissions')->insert(['name' => 'show-position', 'label' => 'Visualizar detalhes da posição', 'group' => 'Posições']);
        DB::table('permissions')->insert(['name' => 'create-position', 'label' => 'Adicionar posições', 'group' => 'Posições']);
        DB::table('permissions')->insert(['name' => 'edit-position', 'label' => 'Editar posições', 'group' => 'Posições']);
        DB::table('permissions')->insert(['name' => 'delete-position', 'label' => 'Remover posições', 'group' => 'Posições']);

        // Permissions for Features
        DB::table('permissions')->insert(['name' => 'index-feature', 'label' => 'Visualizar características', 'group' => 'Características']);
        DB::table('permissions')->insert(['name' => 'show-feature', 'label' => 'Visualizar detalhes da característica', 'group' => 'Características']);
        DB::table('permissions')->insert(['name' => 'create-feature', 'label' => 'Adicionar características', 'group' => 'Características']);
        DB::table('permissions')->insert(['name' => 'edit-feature', 'label' => 'Editar características', 'group' => 'Características']);
        DB::table('permissions')->insert(['name' => 'delete-feature', 'label' => 'Remover características', 'group' => 'Características']);

        // Permissions for Payment Forms
        DB::table('permissions')->insert(['name' => 'index-payment-form', 'label' => 'Visualizar formas de pagamento', 'group' => 'Formas de Pagamento']);
        DB::table('permissions')->insert(['name' => 'show-payment-form', 'label' => 'Visualizar detalhes da forma de pagamento', 'group' => 'Formas de Pagamento']);
        DB::table('permissions')->insert(['name' => 'create-payment-form', 'label' => 'Adicionar formas de pagamento', 'group' => 'Formas de Pagamento']);
        DB::table('permissions')->insert(['name' => 'edit-payment-form', 'label' => 'Editar formas de pagamento', 'group' => 'Formas de Pagamento']);
        DB::table('permissions')->insert(['name' => 'delete-payment-form', 'label' => 'Remover formas de pagamento', 'group' => 'Formas de Pagamento']);

        // Permissions for Field Types
        DB::table('permissions')->insert(['name' => 'index-field-type', 'label' => 'Visualizar tipos de campo', 'group' => 'Tipos de Campo']);
        DB::table('permissions')->insert(['name' => 'show-field-type', 'label' => 'Visualizar detalhes do tipo de campo', 'group' => 'Tipos de Campo']);
        DB::table('permissions')->insert(['name' => 'create-field-type', 'label' => 'Adicionar tipos de campo', 'group' => 'Tipos de Campo']);
        DB::table('permissions')->insert(['name' => 'edit-field-type', 'label' => 'Editar tipos de campo', 'group' => 'Tipos de Campo']);
        DB::table('permissions')->insert(['name' => 'delete-field-type', 'label' => 'Remover tipos de campo', 'group' => 'Tipos de Campo']);

        // Permissions for Field Surfaces
        DB::table('permissions')->insert(['name' => 'index-field-surface', 'label' => 'Visualizar superfícies de campo', 'group' => 'Superfícies de Campo']);
        DB::table('permissions')->insert(['name' => 'show-field-surface', 'label' => 'Visualizar detalhes da superfície de campo', 'group' => 'Superfícies de Campo']);
        DB::table('permissions')->insert(['name' => 'create-field-surface', 'label' => 'Adicionar superfícies de campo', 'group' => 'Superfícies de Campo']);
        DB::table('permissions')->insert(['name' => 'edit-field-surface', 'label' => 'Editar superfícies de campo', 'group' => 'Superfícies de Campo']);
        DB::table('permissions')->insert(['name' => 'delete-field-surface', 'label' => 'Remover superfícies de campo', 'group' => 'Superfícies de Campo']);

        // Permissions for Field Sizes
        DB::table('permissions')->insert(['name' => 'index-field-size', 'label' => 'Visualizar tamanhos de campo', 'group' => 'Tamanhos de Campo']);
        DB::table('permissions')->insert(['name' => 'show-field-size', 'label' => 'Visualizar detalhes do tamanho de campo', 'group' => 'Tamanhos de Campo']);
        DB::table('permissions')->insert(['name' => 'create-field-size', 'label' => 'Adicionar tamanhos de campo', 'group' => 'Tamanhos de Campo']);
        DB::table('permissions')->insert(['name' => 'edit-field-size', 'label' => 'Editar tamanhos de campo', 'group' => 'Tamanhos de Campo']);
        DB::table('permissions')->insert(['name' => 'delete-field-size', 'label' => 'Remover tamanhos de campo', 'group' => 'Tamanhos de Campo']);

        // Permissions for Contact Types
        DB::table('permissions')->insert(['name' => 'index-contact-type', 'label' => 'Visualizar tipos de contato', 'group' => 'Tipos de Contato']);
        DB::table('permissions')->insert(['name' => 'show-contact-type', 'label' => 'Visualizar detalhes do tipo de contato', 'group' => 'Tipos de Contato']);
        DB::table('permissions')->insert(['name' => 'create-contact-type', 'label' => 'Adicionar tipos de contato', 'group' => 'Tipos de Contato']);
        DB::table('permissions')->insert(['name' => 'edit-contact-type', 'label' => 'Editar tipos de contato', 'group' => 'Tipos de Contato']);
        DB::table('permissions')->insert(['name' => 'delete-contact-type', 'label' => 'Remover tipos de contato', 'group' => 'Tipos de Contato']);

        // Permissions for Document Types
        DB::table('permissions')->insert(['name' => 'index-document-type', 'label' => 'Visualizar tipos de documento', 'group' => 'Tipos de Documento']);
        DB::table('permissions')->insert(['name' => 'show-document-type', 'label' => 'Visualizar detalhes do tipo de documento', 'group' => 'Tipos de Documento']);
        DB::table('permissions')->insert(['name' => 'create-document-type', 'label' => 'Adicionar tipos de documento', 'group' => 'Tipos de Documento']);
        DB::table('permissions')->insert(['name' => 'edit-document-type', 'label' => 'Editar tipos de documento', 'group' => 'Tipos de Documento']);
        DB::table('permissions')->insert(['name' => 'delete-document-type', 'label' => 'Remover tipos de documento', 'group' => 'Tipos de Documento']);

        // Permissions for Product Types
        DB::table('permissions')->insert(['name' => 'index-product-type', 'label' => 'Visualizar tipos de produto', 'group' => 'Tipos de Produto']);
        DB::table('permissions')->insert(['name' => 'show-product-type', 'label' => 'Visualizar detalhes do tipo de produto', 'group' => 'Tipos de Produto']);
        DB::table('permissions')->insert(['name' => 'create-product-type', 'label' => 'Adicionar tipos de produto', 'group' => 'Tipos de Produto']);
        DB::table('permissions')->insert(['name' => 'edit-product-type', 'label' => 'Editar tipos de produto', 'group' => 'Tipos de Produto']);
        DB::table('permissions')->insert(['name' => 'delete-product-type', 'label' => 'Remover tipos de produto', 'group' => 'Tipos de Produto']);

        // Permissions for Expense Types
        DB::table('permissions')->insert(['name' => 'index-expense-type', 'label' => 'Visualizar tipos de despesa', 'group' => 'Tipos de Despesa']);
        DB::table('permissions')->insert(['name' => 'show-expense-type', 'label' => 'Visualizar detalhes do tipo de despesa', 'group' => 'Tipos de Despesa']);
        DB::table('permissions')->insert(['name' => 'create-expense-type', 'label' => 'Adicionar tipos de despesa', 'group' => 'Tipos de Despesa']);
        DB::table('permissions')->insert(['name' => 'edit-expense-type', 'label' => 'Editar tipos de despesa', 'group' => 'Tipos de Despesa']);
        DB::table('permissions')->insert(['name' => 'delete-expense-type', 'label' => 'Remover tipos de despesa', 'group' => 'Tipos de Despesa']);

        // Permissions for Field Items
        DB::table('permissions')->insert(['name' => 'index-field-item', 'label' => 'Visualizar itens de campo', 'group' => 'Itens de Campo']);
        DB::table('permissions')->insert(['name' => 'show-field-item', 'label' => 'Visualizar detalhes do item de campo', 'group' => 'Itens de Campo']);
        DB::table('permissions')->insert(['name' => 'create-field-item', 'label' => 'Adicionar itens de campo', 'group' => 'Itens de Campo']);
        DB::table('permissions')->insert(['name' => 'edit-field-item', 'label' => 'Editar itens de campo', 'group' => 'Itens de Campo']);
        DB::table('permissions')->insert(['name' => 'delete-field-item', 'label' => 'Remover itens de campo', 'group' => 'Itens de Campo']);

        // Permissions for Team Types
        DB::table('permissions')->insert(['name' => 'index-team-type', 'label' => 'Visualizar tipos de equipe', 'group' => 'Tipos de Equipe']);
        DB::table('permissions')->insert(['name' => 'show-team-type', 'label' => 'Visualizar detalhes do tipo de equipe', 'group' => 'Tipos de Equipe']);
        DB::table('permissions')->insert(['name' => 'create-team-type', 'label' => 'Adicionar tipos de equipe', 'group' => 'Tipos de Equipe']);
        DB::table('permissions')->insert(['name' => 'edit-team-type', 'label' => 'Editar tipos de equipe', 'group' => 'Tipos de Equipe']);
        DB::table('permissions')->insert(['name' => 'delete-team-type', 'label' => 'Remover tipos de equipe', 'group' => 'Tipos de Equipe']);

        // Permissions for Address Types
        DB::table('permissions')->insert(['name' => 'index-address-type', 'label' => 'Visualizar tipos de endereço', 'group' => 'Tipos de Endereço']);
        DB::table('permissions')->insert(['name' => 'show-address-type', 'label' => 'Visualizar detalhes do tipo de endereço', 'group' => 'Tipos de Endereço']);
        DB::table('permissions')->insert(['name' => 'create-address-type', 'label' => 'Adicionar tipos de endereço', 'group' => 'Tipos de Endereço']);
        DB::table('permissions')->insert(['name' => 'edit-address-type', 'label' => 'Editar tipos de endereço', 'group' => 'Tipos de Endereço']);
        DB::table('permissions')->insert(['name' => 'delete-address-type', 'label' => 'Remover tipos de endereço', 'group' => 'Tipos de Endereço']);

        // Permissions for Contacts
        DB::table('permissions')->insert(['name' => 'index-contact', 'label' => 'Visualizar contatos', 'group' => 'Contatos']);
        DB::table('permissions')->insert(['name' => 'show-contact', 'label' => 'Visualizar detalhes do contato', 'group' => 'Contatos']);
        DB::table('permissions')->insert(['name' => 'create-contact', 'label' => 'Adicionar contatos', 'group' => 'Contatos']);
        DB::table('permissions')->insert(['name' => 'edit-contact', 'label' => 'Editar contatos', 'group' => 'Contatos']);
        DB::table('permissions')->insert(['name' => 'delete-contact', 'label' => 'Remover contatos', 'group' => 'Contatos']);

        // Permissions for Documents
        DB::table('permissions')->insert(['name' => 'index-document', 'label' => 'Visualizar documentos', 'group' => 'Documentos']);
        DB::table('permissions')->insert(['name' => 'show-document', 'label' => 'Visualizar detalhes do documento', 'group' => 'Documentos']);
        DB::table('permissions')->insert(['name' => 'create-document', 'label' => 'Adicionar documentos', 'group' => 'Documentos']);
        DB::table('permissions')->insert(['name' => 'edit-document', 'label' => 'Editar documentos', 'group' => 'Documentos']);
        DB::table('permissions')->insert(['name' => 'delete-document', 'label' => 'Remover documentos', 'group' => 'Documentos']);

        // Permissions for Addresses
        DB::table('permissions')->insert(['name' => 'index-address', 'label' => 'Visualizar endereços', 'group' => 'Endereços']);
        DB::table('permissions')->insert(['name' => 'show-address', 'label' => 'Visualizar detalhes do endereço', 'group' => 'Endereços']);
        DB::table('permissions')->insert(['name' => 'create-address', 'label' => 'Adicionar endereços', 'group' => 'Endereços']);
        DB::table('permissions')->insert(['name' => 'edit-address', 'label' => 'Editar endereços', 'group' => 'Endereços']);
        DB::table('permissions')->insert(['name' => 'delete-address', 'label' => 'Remover endereços', 'group' => 'Endereços']);

        // Permissions for Tags
        DB::table('permissions')->insert(['name' => 'index-tag', 'label' => 'Visualizar tags', 'group' => 'Tags']);
        DB::table('permissions')->insert(['name' => 'show-tag', 'label' => 'Visualizar detalhes da tag', 'group' => 'Tags']);
        DB::table('permissions')->insert(['name' => 'create-tag', 'label' => 'Adicionar tags', 'group' => 'Tags']);
        DB::table('permissions')->insert(['name' => 'edit-tag', 'label' => 'Editar tags', 'group' => 'Tags']);
        DB::table('permissions')->insert(['name' => 'delete-tag', 'label' => 'Remover tags', 'group' => 'Tags']);

        // Permissions for Product Categories
        DB::table('permissions')->insert(['name' => 'index-product-category', 'label' => 'Visualizar categorias de produto', 'group' => 'Categorias de Produto']);
        DB::table('permissions')->insert(['name' => 'show-product-category', 'label' => 'Visualizar detalhes da categoria de produto', 'group' => 'Categorias de Produto']);
        DB::table('permissions')->insert(['name' => 'create-product-category', 'label' => 'Adicionar categorias de produto', 'group' => 'Categorias de Produto']);
        DB::table('permissions')->insert(['name' => 'edit-product-category', 'label' => 'Editar categorias de produto', 'group' => 'Categorias de Produto']);
        DB::table('permissions')->insert(['name' => 'delete-product-category', 'label' => 'Remover categorias de produto', 'group' => 'Categorias de Produto']);

        // Permissions for Companies
        DB::table('permissions')->insert(['name' => 'index-company', 'label' => 'Visualizar empresas', 'group' => 'Empresas']);
        DB::table('permissions')->insert(['name' => 'show-company', 'label' => 'Visualizar detalhes da empresa', 'group' => 'Empresas']);
        DB::table('permissions')->insert(['name' => 'create-company', 'label' => 'Adicionar empresas', 'group' => 'Empresas']);
        DB::table('permissions')->insert(['name' => 'edit-company', 'label' => 'Editar empresas', 'group' => 'Empresas']);
        DB::table('permissions')->insert(['name' => 'delete-company', 'label' => 'Remover empresas', 'group' => 'Empresas']);
        DB::table('permissions')->insert(['name' => 'restore-company', 'label' => 'Restaurar empresas', 'group' => 'Empresas']);
        DB::table('permissions')->insert(['name' => 'force-delete-company', 'label' => 'Excluir permanentemente empresas', 'group' => 'Empresas']);

        // Permissions for Pre Companies Registration
        DB::table('permissions')->insert(['name' => 'index-pre-companies-registration', 'label' => 'Visualizar pré-registros de empresas', 'group' => 'Pré-registros de Empresas']);
        DB::table('permissions')->insert(['name' => 'show-pre-companies-registration', 'label' => 'Visualizar detalhes do pré-registro de empresa', 'group' => 'Pré-registros de Empresas']);
        DB::table('permissions')->insert(['name' => 'create-pre-companies-registration', 'label' => 'Adicionar pré-registros de empresas', 'group' => 'Pré-registros de Empresas']);
        DB::table('permissions')->insert(['name' => 'edit-pre-companies-registration', 'label' => 'Editar pré-registros de empresas', 'group' => 'Pré-registros de Empresas']);
        DB::table('permissions')->insert(['name' => 'delete-pre-companies-registration', 'label' => 'Remover pré-registros de empresas', 'group' => 'Pré-registros de Empresas']);
        DB::table('permissions')->insert(['name' => 'restore-pre-companies-registration', 'label' => 'Restaurar pré-registros de empresas', 'group' => 'Pré-registros de Empresas']);
        DB::table('permissions')->insert(['name' => 'force-delete-pre-companies-registration', 'label' => 'Excluir permanentemente pré-registros de empresas', 'group' => 'Pré-registros de Empresas']);

        // Permissions for Fields
        DB::table('permissions')->insert(['name' => 'index-field', 'label' => 'Visualizar campos', 'group' => 'Campos']);
        DB::table('permissions')->insert(['name' => 'show-field', 'label' => 'Visualizar detalhes do campo', 'group' => 'Campos']);
        DB::table('permissions')->insert(['name' => 'create-field', 'label' => 'Adicionar campos', 'group' => 'Campos']);
        DB::table('permissions')->insert(['name' => 'edit-field', 'label' => 'Editar campos', 'group' => 'Campos']);
        DB::table('permissions')->insert(['name' => 'delete-field', 'label' => 'Remover campos', 'group' => 'Campos']);
        DB::table('permissions')->insert(['name' => 'restore-field', 'label' => 'Restaurar campos', 'group' => 'Campos']);
        DB::table('permissions')->insert(['name' => 'force-delete-field', 'label' => 'Excluir permanentemente campos', 'group' => 'Campos']);

        // Permissions for Field Schedules
        DB::table('permissions')->insert(['name' => 'index-field-schedule', 'label' => 'Visualizar horários dos campos', 'group' => 'Horários dos Campos']);
        DB::table('permissions')->insert(['name' => 'show-field-schedule', 'label' => 'Visualizar detalhes do horário do campo', 'group' => 'Horários dos Campos']);
        DB::table('permissions')->insert(['name' => 'create-field-schedule', 'label' => 'Adicionar horários dos campos', 'group' => 'Horários dos Campos']);
        DB::table('permissions')->insert(['name' => 'edit-field-schedule', 'label' => 'Editar horários dos campos', 'group' => 'Horários dos Campos']);
        DB::table('permissions')->insert(['name' => 'delete-field-schedule', 'label' => 'Remover horários dos campos', 'group' => 'Horários dos Campos']);
        DB::table('permissions')->insert(['name' => 'restore-field-schedule', 'label' => 'Restaurar horários dos campos', 'group' => 'Horários dos Campos']);
        DB::table('permissions')->insert(['name' => 'force-delete-field-schedule', 'label' => 'Excluir permanentemente horários dos campos', 'group' => 'Horários dos Campos']);

        // Permissions for Field Images
        DB::table('permissions')->insert(['name' => 'index-field-image', 'label' => 'Visualizar imagens dos campos', 'group' => 'Imagens dos Campos']);
        DB::table('permissions')->insert(['name' => 'show-field-image', 'label' => 'Visualizar detalhes da imagem do campo', 'group' => 'Imagens dos Campos']);
        DB::table('permissions')->insert(['name' => 'create-field-image', 'label' => 'Adicionar imagens dos campos', 'group' => 'Imagens dos Campos']);
        DB::table('permissions')->insert(['name' => 'edit-field-image', 'label' => 'Editar imagens dos campos', 'group' => 'Imagens dos Campos']);
        DB::table('permissions')->insert(['name' => 'delete-field-image', 'label' => 'Remover imagens dos campos', 'group' => 'Imagens dos Campos']);

        // Permissions for Products
        DB::table('permissions')->insert(['name' => 'index-product', 'label' => 'Visualizar produtos', 'group' => 'Produtos']);
        DB::table('permissions')->insert(['name' => 'show-product', 'label' => 'Visualizar detalhes do produto', 'group' => 'Produtos']);
        DB::table('permissions')->insert(['name' => 'create-product', 'label' => 'Adicionar produtos', 'group' => 'Produtos']);
        DB::table('permissions')->insert(['name' => 'edit-product', 'label' => 'Editar produtos', 'group' => 'Produtos']);
        DB::table('permissions')->insert(['name' => 'delete-product', 'label' => 'Remover produtos', 'group' => 'Produtos']);
        DB::table('permissions')->insert(['name' => 'restore-product', 'label' => 'Restaurar produtos', 'group' => 'Produtos']);
        DB::table('permissions')->insert(['name' => 'force-delete-product', 'label' => 'Excluir permanentemente produtos', 'group' => 'Produtos']);

        // Permissions for Expenses
        DB::table('permissions')->insert(['name' => 'index-expense', 'label' => 'Visualizar despesas', 'group' => 'Despesas']);
        DB::table('permissions')->insert(['name' => 'show-expense', 'label' => 'Visualizar detalhes da despesa', 'group' => 'Despesas']);
        DB::table('permissions')->insert(['name' => 'create-expense', 'label' => 'Adicionar despesas', 'group' => 'Despesas']);
        DB::table('permissions')->insert(['name' => 'edit-expense', 'label' => 'Editar despesas', 'group' => 'Despesas']);
        DB::table('permissions')->insert(['name' => 'delete-expense', 'label' => 'Remover despesas', 'group' => 'Despesas']);
        DB::table('permissions')->insert(['name' => 'restore-expense', 'label' => 'Restaurar despesas', 'group' => 'Despesas']);
        DB::table('permissions')->insert(['name' => 'force-delete-expense', 'label' => 'Excluir permanentemente despesas', 'group' => 'Despesas']);

        // Permissions for Teams
        DB::table('permissions')->insert(['name' => 'index-team', 'label' => 'Visualizar equipes', 'group' => 'Equipes']);
        DB::table('permissions')->insert(['name' => 'show-team', 'label' => 'Visualizar detalhes da equipe', 'group' => 'Equipes']);
        DB::table('permissions')->insert(['name' => 'create-team', 'label' => 'Adicionar equipes', 'group' => 'Equipes']);
        DB::table('permissions')->insert(['name' => 'edit-team', 'label' => 'Editar equipes', 'group' => 'Equipes']);
        DB::table('permissions')->insert(['name' => 'delete-team', 'label' => 'Remover equipes', 'group' => 'Equipes']);
        DB::table('permissions')->insert(['name' => 'restore-team', 'label' => 'Restaurar equipes', 'group' => 'Equipes']);
        DB::table('permissions')->insert(['name' => 'force-delete-team', 'label' => 'Excluir permanentemente equipes', 'group' => 'Equipes']);

        // Permissions for Team Players
        DB::table('permissions')->insert(['name' => 'index-team-player', 'label' => 'Visualizar jogadores da equipe', 'group' => 'Jogadores da Equipe']);
        DB::table('permissions')->insert(['name' => 'show-team-player', 'label' => 'Visualizar detalhes do jogador da equipe', 'group' => 'Jogadores da Equipe']);
        DB::table('permissions')->insert(['name' => 'create-team-player', 'label' => 'Adicionar jogadores à equipe', 'group' => 'Jogadores da Equipe']);
        DB::table('permissions')->insert(['name' => 'edit-team-player', 'label' => 'Editar jogadores da equipe', 'group' => 'Jogadores da Equipe']);
        DB::table('permissions')->insert(['name' => 'delete-team-player', 'label' => 'Remover jogadores da equipe', 'group' => 'Jogadores da Equipe']);
        DB::table('permissions')->insert(['name' => 'restore-team-player', 'label' => 'Restaurar jogadores da equipe', 'group' => 'Jogadores da Equipe']);
        DB::table('permissions')->insert(['name' => 'force-delete-team-player', 'label' => 'Excluir permanentemente jogadores da equipe', 'group' => 'Jogadores da Equipe']);

        // Permissions for Coupons
        DB::table('permissions')->insert(['name' => 'index-coupon', 'label' => 'Visualizar cupons', 'group' => 'Cupons']);
        DB::table('permissions')->insert(['name' => 'show-coupon', 'label' => 'Visualizar detalhes do cupom', 'group' => 'Cupons']);
        DB::table('permissions')->insert(['name' => 'create-coupon', 'label' => 'Adicionar cupons', 'group' => 'Cupons']);
        DB::table('permissions')->insert(['name' => 'edit-coupon', 'label' => 'Editar cupons', 'group' => 'Cupons']);
        DB::table('permissions')->insert(['name' => 'delete-coupon', 'label' => 'Remover cupons', 'group' => 'Cupons']);
        DB::table('permissions')->insert(['name' => 'restore-coupon', 'label' => 'Restaurar cupons', 'group' => 'Cupons']);
        DB::table('permissions')->insert(['name' => 'force-delete-coupon', 'label' => 'Excluir permanentemente cupons', 'group' => 'Cupons']);

        // Permissions for Bookings
        DB::table('permissions')->insert(['name' => 'index-booking', 'label' => 'Visualizar reservas', 'group' => 'Reservas']);
        DB::table('permissions')->insert(['name' => 'show-booking', 'label' => 'Visualizar detalhes da reserva', 'group' => 'Reservas']);
        DB::table('permissions')->insert(['name' => 'create-booking', 'label' => 'Adicionar reservas', 'group' => 'Reservas']);
        DB::table('permissions')->insert(['name' => 'edit-booking', 'label' => 'Editar reservas', 'group' => 'Reservas']);
        DB::table('permissions')->insert(['name' => 'delete-booking', 'label' => 'Remover reservas', 'group' => 'Reservas']);
        DB::table('permissions')->insert(['name' => 'restore-booking', 'label' => 'Restaurar reservas', 'group' => 'Reservas']);
        DB::table('permissions')->insert(['name' => 'force-delete-booking', 'label' => 'Excluir permanentemente reservas', 'group' => 'Reservas']);

        // Permissions for Tabs
        DB::table('permissions')->insert(['name' => 'index-tab', 'label' => 'Visualizar comandas', 'group' => 'Comandas']);
        DB::table('permissions')->insert(['name' => 'show-tab', 'label' => 'Visualizar detalhes da comanda', 'group' => 'Comandas']);
        DB::table('permissions')->insert(['name' => 'create-tab', 'label' => 'Adicionar comandas', 'group' => 'Comandas']);
        DB::table('permissions')->insert(['name' => 'edit-tab', 'label' => 'Editar comandas', 'group' => 'Comandas']);
        DB::table('permissions')->insert(['name' => 'delete-tab', 'label' => 'Remover comandas', 'group' => 'Comandas']);
        DB::table('permissions')->insert(['name' => 'restore-tab', 'label' => 'Restaurar comandas', 'group' => 'Comandas']);
        DB::table('permissions')->insert(['name' => 'force-delete-tab', 'label' => 'Excluir permanentemente comandas', 'group' => 'Comandas']);

        // Permissions for Tab Items
        DB::table('permissions')->insert(['name' => 'index-tab-item', 'label' => 'Visualizar itens de comanda', 'group' => 'Itens de Comanda']);
        DB::table('permissions')->insert(['name' => 'show-tab-item', 'label' => 'Visualizar detalhes do item de comanda', 'group' => 'Itens de Comanda']);
        DB::table('permissions')->insert(['name' => 'create-tab-item', 'label' => 'Adicionar itens de comanda', 'group' => 'Itens de Comanda']);
        DB::table('permissions')->insert(['name' => 'edit-tab-item', 'label' => 'Editar itens de comanda', 'group' => 'Itens de Comanda']);
        DB::table('permissions')->insert(['name' => 'delete-tab-item', 'label' => 'Remover itens de comanda', 'group' => 'Itens de Comanda']);
        DB::table('permissions')->insert(['name' => 'restore-tab-item', 'label' => 'Restaurar itens de comanda', 'group' => 'Itens de Comanda']);
        DB::table('permissions')->insert(['name' => 'force-delete-tab-item', 'label' => 'Excluir permanentemente itens de comanda', 'group' => 'Itens de Comanda']);

        // Permissions for Athlete Profiles
        DB::table('permissions')->insert(['name' => 'index-athlete-profile', 'label' => 'Visualizar perfis de atleta', 'group' => 'Perfis de Atleta']);
        DB::table('permissions')->insert(['name' => 'show-athlete-profile', 'label' => 'Visualizar detalhes do perfil de atleta', 'group' => 'Perfis de Atleta']);
        DB::table('permissions')->insert(['name' => 'create-athlete-profile', 'label' => 'Adicionar perfis de atleta', 'group' => 'Perfis de Atleta']);
        DB::table('permissions')->insert(['name' => 'edit-athlete-profile', 'label' => 'Editar perfis de atleta', 'group' => 'Perfis de Atleta']);
        DB::table('permissions')->insert(['name' => 'delete-athlete-profile', 'label' => 'Remover perfis de atleta', 'group' => 'Perfis de Atleta']);
        DB::table('permissions')->insert(['name' => 'restore-athlete-profile', 'label' => 'Restaurar perfis de atleta', 'group' => 'Perfis de Atleta']);
        DB::table('permissions')->insert(['name' => 'force-delete-athlete-profile', 'label' => 'Excluir permanentemente perfis de atleta', 'group' => 'Perfis de Atleta']);

        // Permissions for Booking Participants
        DB::table('permissions')->insert(['name' => 'index-booking-participant', 'label' => 'Visualizar participantes de reserva', 'group' => 'Participantes de Reserva']);
        DB::table('permissions')->insert(['name' => 'show-booking-participant', 'label' => 'Visualizar detalhes do participante de reserva', 'group' => 'Participantes de Reserva']);
        DB::table('permissions')->insert(['name' => 'create-booking-participant', 'label' => 'Adicionar participantes de reserva', 'group' => 'Participantes de Reserva']);
        DB::table('permissions')->insert(['name' => 'edit-booking-participant', 'label' => 'Editar participantes de reserva', 'group' => 'Participantes de Reserva']);
        DB::table('permissions')->insert(['name' => 'delete-booking-participant', 'label' => 'Remover participantes de reserva', 'group' => 'Participantes de Reserva']);

        // Permissions for Statistics
        DB::table('permissions')->insert(['name' => 'index-statistics', 'label' => 'Visualizar estatísticas', 'group' => 'Estatísticas']);
        DB::table('permissions')->insert(['name' => 'show-statistics', 'label' => 'Visualizar detalhes da estatística', 'group' => 'Estatísticas']);
        DB::table('permissions')->insert(['name' => 'create-statistics', 'label' => 'Adicionar estatísticas', 'group' => 'Estatísticas']);
        DB::table('permissions')->insert(['name' => 'edit-statistics', 'label' => 'Editar estatísticas', 'group' => 'Estatísticas']);
        DB::table('permissions')->insert(['name' => 'delete-statistics', 'label' => 'Remover estatísticas', 'group' => 'Estatísticas']);

        // Permissions for Player Ratings
        DB::table('permissions')->insert(['name' => 'index-player-rating', 'label' => 'Visualizar avaliações de jogadores', 'group' => 'Avaliações de Jogadores']);
        DB::table('permissions')->insert(['name' => 'show-player-rating', 'label' => 'Visualizar detalhes da avaliação de jogador', 'group' => 'Avaliações de Jogadores']);
        DB::table('permissions')->insert(['name' => 'create-player-rating', 'label' => 'Adicionar avaliações de jogadores', 'group' => 'Avaliações de Jogadores']);
        DB::table('permissions')->insert(['name' => 'edit-player-rating', 'label' => 'Editar avaliações de jogadores', 'group' => 'Avaliações de Jogadores']);
        DB::table('permissions')->insert(['name' => 'delete-player-rating', 'label' => 'Remover avaliações de jogadores', 'group' => 'Avaliações de Jogadores']);
        DB::table('permissions')->insert(['name' => 'restore-player-rating', 'label' => 'Restaurar avaliações de jogadores', 'group' => 'Avaliações de Jogadores']);
        DB::table('permissions')->insert(['name' => 'force-delete-player-rating', 'label' => 'Excluir permanentemente avaliações de jogadores', 'group' => 'Avaliações de Jogadores']);

        // Permissions for Player Statistics
        DB::table('permissions')->insert(['name' => 'index-player-statistic', 'label' => 'Visualizar estatísticas de jogadores', 'group' => 'Estatísticas de Jogadores']);
        DB::table('permissions')->insert(['name' => 'show-player-statistic', 'label' => 'Visualizar detalhes da estatística de jogador', 'group' => 'Estatísticas de Jogadores']);
        DB::table('permissions')->insert(['name' => 'create-player-statistic', 'label' => 'Adicionar estatísticas de jogadores', 'group' => 'Estatísticas de Jogadores']);
        DB::table('permissions')->insert(['name' => 'edit-player-statistic', 'label' => 'Editar estatísticas de jogadores', 'group' => 'Estatísticas de Jogadores']);
        DB::table('permissions')->insert(['name' => 'delete-player-statistic', 'label' => 'Remover estatísticas de jogadores', 'group' => 'Estatísticas de Jogadores']);
        DB::table('permissions')->insert(['name' => 'restore-player-statistic', 'label' => 'Restaurar estatísticas de jogadores', 'group' => 'Estatísticas de Jogadores']);
        DB::table('permissions')->insert(['name' => 'force-delete-player-statistic', 'label' => 'Excluir permanentemente estatísticas de jogadores', 'group' => 'Estatísticas de Jogadores']);

        // Permissions for Statistics Team
        DB::table('permissions')->insert(['name' => 'index-statistics-team', 'label' => 'Visualizar estatísticas de time', 'group' => 'Estatísticas de Time']);
        DB::table('permissions')->insert(['name' => 'show-statistics-team', 'label' => 'Visualizar detalhes da estatística de time', 'group' => 'Estatísticas de Time']);
        DB::table('permissions')->insert(['name' => 'create-statistics-team', 'label' => 'Adicionar estatísticas de time', 'group' => 'Estatísticas de Time']);
        DB::table('permissions')->insert(['name' => 'edit-statistics-team', 'label' => 'Editar estatísticas de time', 'group' => 'Estatísticas de Time']);
        DB::table('permissions')->insert(['name' => 'delete-statistics-team', 'label' => 'Remover estatísticas de time', 'group' => 'Estatísticas de Time']);
        DB::table('permissions')->insert(['name' => 'restore-statistics-team', 'label' => 'Restaurar estatísticas de time', 'group' => 'Estatísticas de Time']);
        DB::table('permissions')->insert(['name' => 'force-delete-statistics-team', 'label' => 'Excluir permanentemente estatísticas de time', 'group' => 'Estatísticas de Time']);

        // Permissions for Team Booking
        DB::table('permissions')->insert(['name' => 'index-team-booking', 'label' => 'Visualizar reservas de time', 'group' => 'Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'show-team-booking', 'label' => 'Visualizar detalhes da reserva de time', 'group' => 'Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'create-team-booking', 'label' => 'Adicionar reservas de time', 'group' => 'Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'edit-team-booking', 'label' => 'Editar reservas de time', 'group' => 'Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'delete-team-booking', 'label' => 'Remover reservas de time', 'group' => 'Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'restore-team-booking', 'label' => 'Restaurar reservas de time', 'group' => 'Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'force-delete-team-booking', 'label' => 'Excluir permanentemente reservas de time', 'group' => 'Reservas de Time']);

        // Permissions for Team Statistics Booking
        DB::table('permissions')->insert(['name' => 'index-team-statistics-booking', 'label' => 'Visualizar estatísticas de reservas de time', 'group' => 'Estatísticas de Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'show-team-statistics-booking', 'label' => 'Visualizar detalhes da estatística de reserva de time', 'group' => 'Estatísticas de Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'create-team-statistics-booking', 'label' => 'Adicionar estatísticas de reservas de time', 'group' => 'Estatísticas de Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'edit-team-statistics-booking', 'label' => 'Editar estatísticas de reservas de time', 'group' => 'Estatísticas de Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'delete-team-statistics-booking', 'label' => 'Remover estatísticas de reservas de time', 'group' => 'Estatísticas de Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'restore-team-statistics-booking', 'label' => 'Restaurar estatísticas de reservas de time', 'group' => 'Estatísticas de Reservas de Time']);
        DB::table('permissions')->insert(['name' => 'force-delete-team-statistics-booking', 'label' => 'Excluir permanentemente estatísticas de reservas de time', 'group' => 'Estatísticas de Reservas de Time']);

        // Permissions for Marketing Types
        DB::table('permissions')->insert(['name' => 'index-marketing-type', 'label' => 'Visualizar tipos de marketing', 'group' => 'Tipos de Marketing']);
        DB::table('permissions')->insert(['name' => 'show-marketing-type', 'label' => 'Visualizar detalhes do tipo de marketing', 'group' => 'Tipos de Marketing']);
        DB::table('permissions')->insert(['name' => 'create-marketing-type', 'label' => 'Adicionar tipos de marketing', 'group' => 'Tipos de Marketing']);
        DB::table('permissions')->insert(['name' => 'edit-marketing-type', 'label' => 'Editar tipos de marketing', 'group' => 'Tipos de Marketing']);
        DB::table('permissions')->insert(['name' => 'delete-marketing-type', 'label' => 'Remover tipos de marketing', 'group' => 'Tipos de Marketing']);
        DB::table('permissions')->insert(['name' => 'restore-marketing-type', 'label' => 'Restaurar tipos de marketing', 'group' => 'Tipos de Marketing']);
        DB::table('permissions')->insert(['name' => 'force-delete-marketing-type', 'label' => 'Excluir permanentemente tipos de marketing', 'group' => 'Tipos de Marketing']);

        // Permissions for Marketings
        DB::table('permissions')->insert(['name' => 'index-marketing', 'label' => 'Visualizar marketings', 'group' => 'Marketings']);
        DB::table('permissions')->insert(['name' => 'show-marketing', 'label' => 'Visualizar detalhes do marketing', 'group' => 'Marketings']);
        DB::table('permissions')->insert(['name' => 'create-marketing', 'label' => 'Adicionar marketings', 'group' => 'Marketings']);
        DB::table('permissions')->insert(['name' => 'edit-marketing', 'label' => 'Editar marketings', 'group' => 'Marketings']);
        DB::table('permissions')->insert(['name' => 'delete-marketing', 'label' => 'Remover marketings', 'group' => 'Marketings']);
        DB::table('permissions')->insert(['name' => 'restore-marketing', 'label' => 'Restaurar marketings', 'group' => 'Marketings']);
        DB::table('permissions')->insert(['name' => 'force-delete-marketing', 'label' => 'Excluir permanentemente marketings', 'group' => 'Marketings']);

        // Permissions for Receipts
        DB::table('permissions')->insert(['name' => 'index-receipt', 'label' => 'Visualizar recibos', 'group' => 'Recibos']);
        DB::table('permissions')->insert(['name' => 'show-receipt', 'label' => 'Visualizar detalhes do recibo', 'group' => 'Recibos']);
        DB::table('permissions')->insert(['name' => 'create-receipt', 'label' => 'Adicionar recibos', 'group' => 'Recibos']);
        DB::table('permissions')->insert(['name' => 'edit-receipt', 'label' => 'Editar recibos', 'group' => 'Recibos']);
        DB::table('permissions')->insert(['name' => 'delete-receipt', 'label' => 'Remover recibos', 'group' => 'Recibos']);
        DB::table('permissions')->insert(['name' => 'restore-receipt', 'label' => 'Restaurar recibos', 'group' => 'Recibos']);
        DB::table('permissions')->insert(['name' => 'force-delete-receipt', 'label' => 'Excluir permanentemente recibos', 'group' => 'Recibos']);

        // Permissions for Warehouses
        DB::table('permissions')->insert(['name' => 'index-warehouse', 'label' => 'Visualizar armazéns', 'group' => 'Armazéns']);
        DB::table('permissions')->insert(['name' => 'show-warehouse', 'label' => 'Visualizar detalhes do armazém', 'group' => 'Armazéns']);
        DB::table('permissions')->insert(['name' => 'create-warehouse', 'label' => 'Adicionar armazéns', 'group' => 'Armazéns']);
        DB::table('permissions')->insert(['name' => 'edit-warehouse', 'label' => 'Editar armazéns', 'group' => 'Armazéns']);
        DB::table('permissions')->insert(['name' => 'delete-warehouse', 'label' => 'Remover armazéns', 'group' => 'Armazéns']);
        DB::table('permissions')->insert(['name' => 'restore-warehouse', 'label' => 'Restaurar armazéns', 'group' => 'Armazéns']);
        DB::table('permissions')->insert(['name' => 'force-delete-warehouse', 'label' => 'Excluir permanentemente armazéns', 'group' => 'Armazéns']);

        // Permissions for Stocks
        DB::table('permissions')->insert(['name' => 'index-stock', 'label' => 'Visualizar estoques', 'group' => 'Estoques']);
        DB::table('permissions')->insert(['name' => 'show-stock', 'label' => 'Visualizar detalhes do estoque', 'group' => 'Estoques']);
        DB::table('permissions')->insert(['name' => 'create-stock', 'label' => 'Adicionar estoques', 'group' => 'Estoques']);
        DB::table('permissions')->insert(['name' => 'edit-stock', 'label' => 'Editar estoques', 'group' => 'Estoques']);
        DB::table('permissions')->insert(['name' => 'delete-stock', 'label' => 'Remover estoques', 'group' => 'Estoques']);
        DB::table('permissions')->insert(['name' => 'restore-stock', 'label' => 'Restaurar estoques', 'group' => 'Estoques']);
        DB::table('permissions')->insert(['name' => 'force-delete-stock', 'label' => 'Excluir permanentemente estoques', 'group' => 'Estoques']);

        // Permissions for Suppliers
        DB::table('permissions')->insert(['name' => 'index-supplier', 'label' => 'Visualizar fornecedores', 'group' => 'Fornecedores']);
        DB::table('permissions')->insert(['name' => 'show-supplier', 'label' => 'Visualizar detalhes do fornecedor', 'group' => 'Fornecedores']);
        DB::table('permissions')->insert(['name' => 'create-supplier', 'label' => 'Adicionar fornecedores', 'group' => 'Fornecedores']);
        DB::table('permissions')->insert(['name' => 'edit-supplier', 'label' => 'Editar fornecedores', 'group' => 'Fornecedores']);
        DB::table('permissions')->insert(['name' => 'delete-supplier', 'label' => 'Remover fornecedores', 'group' => 'Fornecedores']);
        DB::table('permissions')->insert(['name' => 'restore-supplier', 'label' => 'Restaurar fornecedores', 'group' => 'Fornecedores']);
        DB::table('permissions')->insert(['name' => 'force-delete-supplier', 'label' => 'Excluir permanentemente fornecedores', 'group' => 'Fornecedores']);

        // Permissions for Purchases
        DB::table('permissions')->insert(['name' => 'index-purchase', 'label' => 'Visualizar compras', 'group' => 'Compras']);
        DB::table('permissions')->insert(['name' => 'show-purchase', 'label' => 'Visualizar detalhes da compra', 'group' => 'Compras']);
        DB::table('permissions')->insert(['name' => 'create-purchase', 'label' => 'Adicionar compras', 'group' => 'Compras']);
        DB::table('permissions')->insert(['name' => 'edit-purchase', 'label' => 'Editar compras', 'group' => 'Compras']);
        DB::table('permissions')->insert(['name' => 'delete-purchase', 'label' => 'Remover compras', 'group' => 'Compras']);
        DB::table('permissions')->insert(['name' => 'restore-purchase', 'label' => 'Restaurar compras', 'group' => 'Compras']);
        DB::table('permissions')->insert(['name' => 'force-delete-purchase', 'label' => 'Excluir permanentemente compras', 'group' => 'Compras']);

        // Permissions for Stock Movements
        DB::table('permissions')->insert(['name' => 'index-stock-movement', 'label' => 'Visualizar movimentações de estoque', 'group' => 'Movimentações de Estoque']);
        DB::table('permissions')->insert(['name' => 'show-stock-movement', 'label' => 'Visualizar detalhes da movimentação de estoque', 'group' => 'Movimentações de Estoque']);
        DB::table('permissions')->insert(['name' => 'create-stock-movement', 'label' => 'Adicionar movimentações de estoque', 'group' => 'Movimentações de Estoque']);
        DB::table('permissions')->insert(['name' => 'edit-stock-movement', 'label' => 'Editar movimentações de estoque', 'group' => 'Movimentações de Estoque']);
        DB::table('permissions')->insert(['name' => 'delete-stock-movement', 'label' => 'Remover movimentações de estoque', 'group' => 'Movimentações de Estoque']);
        DB::table('permissions')->insert(['name' => 'restore-stock-movement', 'label' => 'Restaurar movimentações de estoque', 'group' => 'Movimentações de Estoque']);
        DB::table('permissions')->insert(['name' => 'force-delete-stock-movement', 'label' => 'Excluir permanentemente movimentações de estoque', 'group' => 'Movimentações de Estoque']);

        // Permissions for Tournaments
        DB::table('permissions')->insert(['name' => 'index-tournament', 'label' => 'Visualizar torneios', 'group' => 'Torneios']);
        DB::table('permissions')->insert(['name' => 'show-tournament', 'label' => 'Visualizar detalhes do torneio', 'group' => 'Torneios']);
        DB::table('permissions')->insert(['name' => 'create-tournament', 'label' => 'Adicionar torneios', 'group' => 'Torneios']);
        DB::table('permissions')->insert(['name' => 'edit-tournament', 'label' => 'Editar torneios', 'group' => 'Torneios']);
        DB::table('permissions')->insert(['name' => 'delete-tournament', 'label' => 'Remover torneios', 'group' => 'Torneios']);
        DB::table('permissions')->insert(['name' => 'restore-tournament', 'label' => 'Restaurar torneios', 'group' => 'Torneios']);
        DB::table('permissions')->insert(['name' => 'force-delete-tournament', 'label' => 'Excluir permanentemente torneios', 'group' => 'Torneios']);

        // Permissions for Tournament Teams
        DB::table('permissions')->insert(['name' => 'index-tournament-team', 'label' => 'Visualizar equipes de torneios', 'group' => 'Equipes de Torneios']);
        DB::table('permissions')->insert(['name' => 'show-tournament-team', 'label' => 'Visualizar detalhes da equipe de torneio', 'group' => 'Equipes de Torneios']);
        DB::table('permissions')->insert(['name' => 'create-tournament-team', 'label' => 'Adicionar equipes de torneios', 'group' => 'Equipes de Torneios']);
        DB::table('permissions')->insert(['name' => 'edit-tournament-team', 'label' => 'Editar equipes de torneios', 'group' => 'Equipes de Torneios']);
        DB::table('permissions')->insert(['name' => 'delete-tournament-team', 'label' => 'Remover equipes de torneios', 'group' => 'Equipes de Torneios']);
        DB::table('permissions')->insert(['name' => 'restore-tournament-team', 'label' => 'Restaurar equipes de torneios', 'group' => 'Equipes de Torneios']);
        DB::table('permissions')->insert(['name' => 'force-delete-tournament-team', 'label' => 'Excluir permanentemente equipes de torneios', 'group' => 'Equipes de Torneios']);
    }
}