import { IReserve } from "@/typings/reserve";
import request from "@/utils/request";
import { AxiosPromise } from "axios";

function getReserveList(page: number, limit: number): AxiosPromise<any> {
    const data = {
        page: page,
        limit: limit
    }
    return request("reserve/list", "get", data);
}

function addReserve(data: IReserve): AxiosPromise<any> {
    return request("reserve", "post", data);
}

function setReserve(data: IReserve): AxiosPromise<any> {
    return request("reserve/"+data.id, "put", data);
}

function removeReserve(id: number): AxiosPromise<any> {
    return request("reserve/" + id, "delete", {});
}

export {
    getReserveList,
    addReserve,
    setReserve,
    removeReserve
}