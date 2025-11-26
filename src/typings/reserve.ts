export interface IReserve {
    id?: number,
    shop_id: number | null,
    shop_name: string,
    shop_room: string,
    order_day?: string,
    order_number?: string,
    order_phone?: string,
    order_time?: string,
    order_time_str?:string,
    order_user?: string,
    update_time?: string
}

export interface IQuery {
    code?: string,
    page: number,
    limit: number
}