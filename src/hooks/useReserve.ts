/*
 ************************************************
 * 
 * 菜单Reserve相关操作
 * Author: GuWenjun
 * Time: 2024-12-09
 * 
 * ***********************************************
 */

import { IReserve } from '@/typings/reserve';
import { IShopTree } from '@/typings/shop';
import {
    getReserveList as getReserveListRequest,
    addReserve as addReserveRequest,
    setReserve as setReserveRequest,
    removeReserve as removeReserveRequest

} from '@/api/reserve';
import { getShopAndRoom as getShopAndRoomRequest } from '@/api/shop';



export const useReserve = () => {
    const orderTimeStr = ['早餐', '午餐', '晚餐'];
    const orderTime = ['6:00', '6:30', '7:00', '7:30', '8:00', '8:30', '9:00', '9:30', '10:00', '10:30', '11:00',
        '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00',
        '17:30', '18:00', '18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00',]

    // 获取列表(带翻页)
    const getReserveList = (page:number,limit:number) => {
        // console.log('getReserveList')
        // return 'aaaaabbbbb';

        return new Promise<IReserve[]>((resolve, reject) => {
            getReserveListRequest(page, limit).then((res: any) => {
                if (res.code < 0) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    // 单个详情
    const getReserveInfo = () => {
        console.log('getReserveInfo')
    }

    // 编辑
    const addReserve = (data:IReserve) => {
        return new Promise<any>((resolve, reject) => {
            addReserveRequest(data).then((res: any) => {
                if (res.code) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }
    const setReserve = (data:IReserve) => {
        return new Promise<any>((resolve, reject) => {
            setReserveRequest(data).then((res: any) => {
                if (res.code) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    // 删除
    const deleteReserve = (id:number) => {
        return new Promise<any>((resolve, reject) => {
            removeReserveRequest(id).then((res: any) => {
                if (res.code) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    const getShopsAndRooms = () => {
        return new Promise<IShopTree[]>((resolve, reject) => {
            getShopAndRoomRequest().then((res: any) => {
                if (res.code < 0) {
                    reject(res);
                }
                resolve(res.data);
            });
        })
    }

    return {
        getReserveList,
        getReserveInfo,
        addReserve,
        setReserve,
        deleteReserve,
        getShopsAndRooms,
        orderTimeStr,
        orderTime
    }
}