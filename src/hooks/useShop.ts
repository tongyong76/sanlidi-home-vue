/*
 ************************************************
 * 
 * 店铺相关操作
 * Author: GuWenjun
 * Time: 2024-12-09
 * 
 * ***********************************************
 */

import { IShop, IRoom } from '@/typings/shop';
import {
    getShopList as getShopListRequest,
    setShop as setShopRequest,
    addRoom as addRoomRequest,
    setRoom as setRoomRequest
} from '@/api/shop';

export const useShop = () => { 
    const getShopList = () => {
        return new Promise<IShop[]>((resolve, reject) => {
            getShopListRequest().then((res: any) => {
                if (res.code < 0) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    const addShop = (data:IShop) => {
        console.log('addShop',data)
    }

    const setShop = (data:IShop) => {
        return new Promise<IShop[]>((resolve, reject) => {
            setShopRequest(data).then((res: any) => {
                if (res.code) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    const deleteShop = (id:number) => {
        console.log('delShop' +id)
    }

    // const getShopAndRoom = () => {
        
    // }

    const addRoom = (data: IRoom) => {
        return new Promise<IShop[]>((resolve, reject) => {
            addRoomRequest(data).then((res: any) => {
                if (res.code) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    const setRoom = (data: IRoom) => {
        return new Promise<IShop[]>((resolve, reject) => {
            setRoomRequest(data).then((res: any) => {
                if (res.code) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    return {
        getShopList,
        addShop,
        setShop,
        deleteShop,
        addRoom,
        setRoom
    }
}