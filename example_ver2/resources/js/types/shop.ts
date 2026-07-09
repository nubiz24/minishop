export type Product = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    stock: number;
    image_url: string | null;
    is_active: boolean;
    created_at?: string | null;
};

export type CartLine = {
    product: Product;
    quantity: number;
    subtotal: number;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type AdminUser = {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
    orders_count: number;
    created_at: string | null;
};

export type OrderSummary = {
    id: number;
    customer_name: string;
    customer_email: string;
    status: string;
    total_amount: number;
    created_at: string | null;
    user: {
        id: number;
        name: string;
    } | null;
};

export type OrderItem = {
    id?: number;
    product_name: string;
    price: number;
    quantity: number;
    subtotal: number;
};

export type OrderDetail = OrderSummary & {
    customer_phone: string | null;
    shipping_address: string;
    note: string | null;
    items: OrderItem[];
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
};

export type CustomerOrderSummary = {
    id: number;
    status: string;
    status_label: string;
    total_amount: number;
    created_at: string | null;
    items_count: number;
};

export type CustomerOrderDetail = {
    id: number;
    customer_name: string;
    customer_email: string;
    customer_phone: string | null;
    shipping_address: string;
    note: string | null;
    status: string;
    status_label: string;
    total_amount: number;
    created_at: string | null;
    items: OrderItem[];
};
