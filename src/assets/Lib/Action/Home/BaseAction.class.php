<?php
//首页
class BaseAction extends Action
{
    public function _initialize()
    {
        header("Content-Type:text/html; charset=UTF-8");

        // //判断站点状态
        // // if(C('site_status')==0){
        // // echo '<div style="whdth:100%;height:100%;font-size:60;color:#5FAA92;">网站维护中...</div>';
        // // exit();
        // // }

        // //判断是否登录
        // //var_dump($_COOKIE);
        // $user_mod = M("User");
        // $uid = $_COOKIE['id'];
        // if ($uid) {
        //     $nav_user_info = $user_mod->where("id=$uid and is_del=0")->find();
        //     $birthday = explode('-', $nav_user_info['birthday']);
        //     $nav_user_info['byear'] = $birthday[0];
        //     $nav_user_info['bmonth'] = $birthday[1];
        //     $nav_user_info['bday'] = $birthday[2];
        //     if (!$nav_user_info) {
        //         setCookie('id', null, time() - 1, '/');
        //         setCookie('name', null, time() - 1, '/');
        //         $url = C('site_domain');
        //         header('location:' . $url);
        //     }
        //     $this->assign("uid", $uid);
        //     if ($nav_user_info['nickname']) {
        //         $this->assign("uname", $nav_user_info['nickname']);
        //     } else {
        //         $this->assign("uname", substr($nav_user_info['phone'], 0, 3) . '****' . substr($nav_user_info['phone'], 7));
        //     }
        //     $this->assign("nav_user_info", $nav_user_info);
        // } else {
        //     $this->assign("uid", 0);
        // }

        // //载入导航
        // $navMod = M('navigation');
        // $navList = $navMod->where('is_del=0')->order('ordid desc')->select();
        // $this->assign('navList', $navList);

        // //搜索推广
        // $adSearch = M('ad_search')->where('id=1')->find();
        // $this->assign('search', $adSearch);

        // //右侧相关
        // //hot 按favs降序排列
        // $goodsMod = M('goods');
        // $hot = $goodsMod->order('favs desc')->limit(5)->select();
        // $this->assign('hot', $hot);

        // //顶部分类调用
        // $lineCateList = $this->genTree5("goods_cate");
        // $this->assign("lineCateList", $lineCateList);
        // //$ress = sort($lineCateList[1]['son']);
        // $arrr = my_mul_sort($lineCateList[2]['son'], 'ordid');
        // $this->assign("zbCateArr", my_mul_sort($lineCateList[1]['son'], 'ordid'));
        // $this->assign("gnCateArr", my_mul_sort($lineCateList[2]['son'], 'ordid'));
        // $this->assign("cjCateArr", my_mul_sort($lineCateList[3]['son'], 'ordid'));
        // $this->assign("tdCateArr", my_mul_sort($lineCateList[97]['son'], 'ordid'));

        // //广告调用
        // $adList = $this->getAd();
        // $this->assign('ad', $adList);

        // //获取来源
        // if ($_REQUEST['from']) {
        //     session('ordacc', $_REQUEST['from']);
        // }

        // if (!session('ordacc')) {
        //     $url = $_SERVER['HTTP_REFERER'];
        //     $search = "/^(https?:\/\/)?([^\/]+)/i";
        //     preg_match($search, $url, $arr);
        //     if ($arr[2]) {
        //         $ordacc = $arr[2];
        //         session('ordacc', $ordacc);
        //     }
        // }
    }

    /**
     * 404
     */
    public function _empty()
    {
        header("HTTP/1.0 404 Not Found");
        $this->display("Public:404");
    }

    /**
     * 广告集
     */
    public function getAd()
    {
        //if(session('adlist')){
        //    $nlist = session('adlist');
        //}else{
        $list = M('ad')->where('is_del=0 and status =1')->select();
        foreach ($list as $key => $value) {
            $nlist[$value['cname']] = $value;
        }
        //    session('adlist',$nlist);
        //}
        return $nlist;
    }

    /**
     * 无限分类数据树形格式化
     * @access public
     * @param integer $cateMod 分类模型
     * @return array
     */
    public function genTree5($cateMod)
    {
        if (session('tree' . $cateMod)) {
            $items = session('tree' . $cateMod);
        } else {
            $itemss = M($cateMod)->where('is_del=0 and is_auto=0 and is_show=1')->order('ordid desc')->select(); //is_auto=0 不显示旅游圈分类
            foreach ($itemss as $key => $value) {
                $items[$value['id']] = $value;
            }
            session('tree' . $cateMod, $items);
        }
        //genTree5
        foreach ($items as $id => $item) {
            $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
        }

        return isset($items[0]['son']) ? $items[0]['son'] : array();
    }

    /**
     * 根据CID获取分类下的线路
     * @access public
     * @param integer $cid 分类ID
     * @return array
     */
    public function getLinesById($cid, $num = 10)
    {
        $lineMod = M('goods');
        $cateMod = M('goods_cate');
        $map['is_del'] = 0;
        $map['is_show'] = 1;
        $map['minprice'] = array('neq', 0);

        $floor = $cateMod->where('id=' . $cid)->getfield('floor');
        if ($floor == 1) {
            $map['type_id'] = $cid;
        }
        if ($floor == 2) {
            $cate_arr = $cateMod->where('pid=' . $cid)->select();
            if ($cate_arr) {
                foreach ($cate_arr as $key => $value) {
                    $cate_one[] = $value['id'];
                }

                $map['cate_id'] = array('in', $cate_one);
            } else {
                $map['cate_id'] = $cid;
            }
        }
        if ($floor == 3) {
            $map['cate_id'] = $cid;
        }
        $list = $lineMod->where($map)->order('ordid desc')->limit($num)->select();
        return $list;
    }

    public function verify()
    {
        import('ORG.Util.Image');
        Image::buildImageVerify();
        //$this->assign('verify',session('verify'));
        //echo session('verify');
    }

    /**
     * 获取公司最新新闻
     * @access public
     * @param integer $num 显示数量
     * @return array
     * @order add_time desc 按add_time降序排列
     */
    public function getNews($num)
    {
        $mod = M('article');
        $hotList = $mod->field('id,title,add_time')->where('is_del=0 and cate_id=1')->order('ordid desc,add_time desc')->limit($num)->select();
        return $hotList;
    }

    /**
     * 获取最近行程
     * @access public
     * @param integer $id 线路id
     * @return query
     */
    public function getDeparture($id, $sday, $length = 6)
    {
        $nowtime = strtotime(date(Ymd));
        $exptime = $nowtime + 3600 * 24 * $sday;
        $query = '';
        $mod = M('departure_time');
        $list = $mod->where('pid=' . $id . ' and departure_time>=' . $exptime . ' and is_del=0')->order('departure_time')->limit(7)->select();
        //$this->assign('firstDep',date('Y-m-d',$list[0]['departure_time']));
        foreach ($list as $key => $value) {
            if ($key == 0) {
                $query .= date('n/d', $value['departure_time']);
            }

            if ($key < $length and $key > 0) {
                $query .= '，' . date('n/d', $value['departure_time']);
            }

            if ($key == $length) {
                $query .= '...';
            }

        }
        return $query;
        //return date('Ymd',$exptime);
    }

    public function erweima($url, $sn)
    {
        $imgurl = 'Uploads/erweima/' . $sn . '.png';

        if (!file_exists($imgurl)) {
            vendor("phpqrcode.phpqrcode");
            $data = $url;
            $level = 'H';
            $size = 10;
            QRcode::png($data, 'ewm.png', $level, $size, 1);
            $logo = "qrlogo2.gif";
            $qr = 'ewm.png';

            if ($logo !== false) {
                $qr = imagecreatefromstring(file_get_contents($qr));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $qr_width = imagesx($qr);
                $qr_height = imagesy($qr);
                $logo_width = imagesx($logo);
                $logo_height = imagesy($logo);
                $logo_qr_width = 110;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                //echo $logo_qr_height;
                $from_width = ($qr_width - $logo_qr_width) / 2;
                imagecopyresampled($qr, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
                //var_dump($qr);
            }
            imagepng($qr, $imgurl);
        }
        return $imgurl;
    }

    //短信
    public function smsxwkj($phone, $data)
    {
        //include("postmsg.php");
        import("@.ORG.Message");
        $mobile = $phone;
        $content = $data;
        $mess = new mess();
        $mess->_postSingle($mobile, $content);
        //$mess->_getResponse();
    }

    public function page($param)
    {
        extract($param);
        import("@.ORG.Page");
        //总记录数
        $flag = is_string($result);
        $listvar = $listvar ? $listvar : 'list';
        $listRows = $listRows ? $listRows : 10;
        if ($flag) {
            $totalRows = M()->table($result . ' a')->count();
        } else {
            $totalRows = ($result) ? count($result) : 1;
        }

        //创建分页对象
        if ($target && $pagesId) {
            $p = new Page($totalRows, $listRows, $parameter, $url, $target, $pagesId);
        } else {
            $p = new Page($totalRows, $listRows, $parameter, $url);
        }

        //抽取数据
        if ($flag) {
            $result .= " LIMIT {$p->firstRow},{$p->listRows}";
            $voList = M()->query($result);
        } else {
            $voList = array_slice($result, $p->firstRow, $p->listRows);
        }
        $pages = C('PAGE'); //要ajax分页配置PAGE中必须theme带%ajax%，其他字符串替换统一在配置文件中设置，
        //可以使用该方法前用C临时改变配置
        foreach ($pages as $key => $value) {
            $p->setConfig($key, $value); // 'theme'=>'%upPage% %linkPage% %downPage% %ajax%'; 要带 %ajax%
        }
        //分页显示
        $page = $p->show();
        //模板赋值
        $this->assign($listvar, $voList);
        $this->assign("page", $page);
        if ($this->isAjax()) { //判断ajax请求
            layout(false);
            $template = (!$template) ? 'ajaxlist' : $template;
            exit($this->fetch($template));
        }
        return $voList;
    }

    /**
     *     特价推广
     *    $num 显示条数 INT
     *    array
     */
    public function tejia($num)
    {
        $tj = M('goods')->where('is_del=0 and is_show=1 and minprice<>0 and switch like "%2%" and type_id<>97')->order('ordid desc')->limit($num)->select();
        foreach ($tj as $key => $value) {
            switch ($value['type_id']) {
                case 1:
                    $tj[$key]['pinyin'] = 'zhoubian';
                    break;
                case 2:
                    $tj[$key]['pinyin'] = 'guonei';
                    break;
                case 3:
                    $tj[$key]['pinyin'] = 'chujing';
                    break;
                case 326:
                    $tj[$key]['pinyin'] = 'suzhou';
                    break;
            }
        }
        return $tj;
    }
}
