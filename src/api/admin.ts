import { AxiosPromise } from 'axios'
import request from "@/utils/request";

// interface ILogin {
//     phone: string;
//     password: string;
//     domain: string;
//  }

export function getAdminInfo(): AxiosPromise<unknown>{
    return request("rule/getAdminInfo", "get", '');
}

export function getRules(): AxiosPromise<unknown>{
    return request("rule/getRules", "get", '');
}

export function resetPassword(newPassword:string): AxiosPromise<unknown>{
    return request("user/changePwd", "put", {password:newPassword});
}