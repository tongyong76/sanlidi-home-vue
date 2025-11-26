import { IShop,IRoom } from "@/typings/shop";
import request from "@/utils/request";
import { AxiosPromise } from "axios";

function setShopImage(id: number,type:string,images: string): AxiosPromise<any> { 
    return request("shop/image", "put", {id:id,type:type,images:images});
}

function getShopList(): AxiosPromise<IShop[]> {
    return request("shop/list", "get", {});
}

function addShop(data: IShop): AxiosPromise<any> {
    return request("shop", "post", data);
}
function setShop(data: IShop): AxiosPromise<any> {
    return request("shop/"+data.id, "put", data);
}

function removeShop(id: number): AxiosPromise<any> {
    return request("shop/" + id, "delete", {});
}

function getShopAndRoom(): AxiosPromise<any> {
    return request("shop/roomtree", "get", {});
}

function addRoom(data: IRoom): AxiosPromise<any> {
    return request("room", "post", data);
}
function setRoom(data: IRoom): AxiosPromise<any> {
    return request("room/"+data.id, "put", data);
}
function removeRoom(id: number): AxiosPromise<any> {
    return request("room/"+id, "delete", {});
}

export {
    getShopAndRoom,
    setShopImage,
    getShopList,
    addShop,
    setShop,
    removeShop,
    addRoom,
    setRoom,
    removeRoom
}