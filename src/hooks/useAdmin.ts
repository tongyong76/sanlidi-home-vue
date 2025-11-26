/*
 ************************************************
 * 
 * 管理员/权限相关
 * Author: GuWenjun
 * Time: 2022-07-06
 * 
 * ***********************************************
 */

import {getAdminInfo,getRules} from '@/api/admin'

export default class Admin {

    /**
     * 1.管理员信息
     * 2.管理员权限
     * 3.管理员操作
     */

    /**
     * 获取管理员信息
     */
    static  getAdminInfo()
    {
        return new Promise((resolve, reject) => {
            getAdminInfo().then((res:any) => {
                if (res.code === 0) {
                    console.log(res.data)
                    resolve('getAdminInfoSuccess');
                } else {
                    reject(res.message)
                }
            })
        }); 
    }

    /**
     * 获取权限/菜单
     */
    static getRules()
    {
        return new Promise((resolve) => {
            getRules().then(res=>{
                console.log(res)
                resolve('getRulesSuccess');
            })
        }); 
    }
}