# ⛩ TANA - Sistema de Reservas para Restaurantes

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/docs/12.x)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://www.php.net/releases/8.2/en.php)

<img src="./docs/assets/TANA-logo.svg" alt="TANA Logo" width="450"/>

## 🎯 Visão Geral

TANA ("tá na mesa") é um sistema de gerenciamento de reservas para restaurantes, permitindo que estabelecimentos administrem suas mesas e reservas, enquanto clientes podem buscar e reservar lugares de forma simples.

## ⚡ Features da v1.0

### Implementado
- ✅ **Gestão de Restaurantes**: Cadastro completo com configurações
- ✅ **Sistema de Mesas**: Capacidade, tipos (internal, external, vip, window)
- ✅ **CRUD de Mesas**: Interface completa para gerenciar mesas
- ✅ **Sistema Multi-Admin**: Restaurant admins gerenciam seus restaurantes
- ✅ **Edição de Políticas**: Admins podem editar configurações de reserva
- ✅ **Reservas**: Status completo (pending, confirmed, cancelled, completed, no_show)
- ✅ **Usuários Guest**: Permite reservas sem cadastro
- ✅ **Validação de Disponibilidade**: Checagem automática de conflitos
- ✅ **Soft Deletes**: Em todas as entidades principais
- ✅ **Seeders Completos**: Dados de exemplo prontos para demonstração

### Tecnologias
- **Backend**: Laravel 12, PHP 8.2
- **Database**: MySQL (via Sail)
- **Auth**: Laravel Fortify
- **Frontend** (preparado): Inertia.js + Vue 3

## 📦 Instalação Rápida

```bash
# Clone o repositório
git clone <repo-url> tana
cd tana

# Subir containers
./vendor/bin/sail up -d

# Rodar migrations e seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Instalar dependências frontend (quando necessário)
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

## 🔐 Credenciais de Teste

| Tipo | Email | Senha |
|------|-------|-------|
| Root Admin | root@tana.com | password |
| Restaurant Admin (Bistro) | joao@bistro.com | password |
| Restaurant Admin (Trattoria) | maria@trattoria.com | password |
| Cliente 1 | carlos@example.com | password |
| Cliente 2 | ana@example.com | password |
| Cliente 3 | roberto@example.com | password |

## 🔐 Modelo de Permissões

### Root Admin
- **Criar e deletar restaurantes** (administração de alto nível)
- **Visualizar tudo** no sistema (read-only)
- **NÃO pode** gerenciar mesas ou editar políticas de restaurantes

### Restaurant Admin
- **CRUD completo** em seu(s) restaurante(s)
- **Gerenciar mesas**: Criar, editar, deletar mesas
- **Editar políticas**: Configurações de reserva e cancelamento
- **Gerenciar reservas**: Confirmar, cancelar, visualizar

### Customer
- **Visualizar** restaurantes e disponibilidade
- **Fazer reservas** (como guest ou autenticado)
- **Gerenciar** suas próprias reservas

## 🗄️ Estrutura do Banco

```
                ┌─────────────┐
                │   Users     │
                └──┬────────┬─┘
                   │        │
              1:N │        │ N:M (restaurant_admin)
                   │        │
┌──────────────────▼──┐  ┌──▼──────────────┐
│  Reservations       │  │  Restaurants    │
└──────┬──────────────┘  └──┬──────────────┘
       │                    │
       │ N:1             N:1│
       │                    │
       │  ┌──────────────┐  │
       └─►│   Tables     │◄─┘
          └──────────────┘
```

## 📊 Dados de Exemplo

**2 Restaurantes:**
- Bistro do João (6 mesas, 2-8 pessoas)
- Trattoria da Maria (6 mesas, confirmação automática)

**2 Restaurant Admins** (um por restaurante)

**12 Mesas** distribuídas entre os restaurantes

**6 Reservas** com diferentes status e datas

## 🚀 Comandos Úteis

```bash
# Verificar dados no banco
./vendor/bin/sail artisan tinker
>>> Restaurant::with('tables')->get()
>>> Reservation::with('user', 'restaurant', 'table')->get()

# Rodar formatação de código
./vendor/bin/sail composer pint

# Criar nova migration
./vendor/bin/sail artisan make:migration create_something_table

# Criar model com factory e migration
./vendor/bin/sail artisan make:model NomeModel -mf
```

## 📐 Arquitetura de Decisão

### Implementado na v1.0

**Funcionalidades Core:**
- ✅ **Autenticação**: Sistema de login para root e clientes
- ✅ **Gestão de Restaurantes**: CRUD completo via interface web
- ✅ **Gestão de Mesas**: Administração de mesas por restaurante
- ✅ **Sistema de Reservas**: Criação, visualização e gerenciamento via telas
- ✅ **Validação de Disponibilidade**: Checagem automática de conflitos
- ✅ **Painel Administrativo**: Interface web para todas as operações

**Para Versões Futuras:**
- 🔜 Horários de funcionamento complexos (operating_hours)
- 🔜 Logs de auditoria (reservation_logs)
- 🔜 Two-Factor Authentication

**Decisão:** A v1.0 foca no core funcional com interface completa. Recursos avançados podem ser reintroduzidos facilmente pela arquitetura modular.

## 🎨 Pontos Técnicos

1. **Arquitetura Escalável**: Fácil adicionar features complexas
2. **Código Limpo**: PSR-12, Laravel Pint configurado
3. **Type Safety**: Type hints em todos os métodos
4. **Soft Deletes**: Nenhum dado é perdido permanentemente
5. **Guest Support**: UX melhorada para não cadastrados
