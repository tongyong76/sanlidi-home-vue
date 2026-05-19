<?php
//首页
class IndexAction extends BaseAction
{
    public function index()
    {
        //   //判断是否移动端
        //   if (isPhone()) {
        //       //if(0){
        //       header("Location: http://m.33ly.com");
        //   } else {
        //       $lineMod = M('goods');

        //       //首页幻灯调用
        //       $bannerList = M('banner')->where('is_show=1 and is_del=0 and pid=0')->order('ordid desc')->limit(5)->select();
        //       foreach ($bannerList as $key => $value) {
        //           $bannerList[$key]['son'] = M('banner')->where('is_show=1 and is_del=0 and pid=' . $value['id'])->limit(3)->select();
        //       }
        //       $this->assign('bannerList', $bannerList);

        //       //楼层调用（延迟加载）
        //       $this->assign("cjList", $this->getLinesByTypeId(3));
        //       $this->assign("gnList", $this->getLinesByTypeId(2));
        //       $this->assign("zbList", $this->getLinesByTypeId(1));

        //       //推荐调用

        //       //当季热推
        //       $hotMod = new Model();
        //       $sql = "select *
        // from 33_goods as g
        // where g.is_del=0 and g.is_hot=1 and g.is_show=1 and g.minprice<>0 and g.GroupId=0
        // order by g.ordid desc,g.add_time desc
        // limit 0,5";
        //       $hotList = $hotMod->query($sql);
        //       //$hotList = $lineMod->where('is_del=0 and is_hot=1')->limit(5)->select();
        //       foreach ($hotList as $key => $value) {
        //           $hotList[$key]['info'] = msubstr(strip_tags($value['info']), 45);
        //           $hotList[$key]['pinyin'] = M('goods_cate')->where('id=' . $value['type_id'])->getfield('pinyin');
        //           $hotList[$key]['pinyin'] = str_replace("you", "", $hotList[$key]['pinyin']);
        //           $hotList[$key]['switch'] = json_decode($value['switch']);
        //       }
        //       $this->assign('hotList', $hotList);

        //       //文章调用
        //       // $arcList = M('article')->where('cate_id=1 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->limit(5)->select();
        //       // $this->assign('arcList',$arcList);
        //       $this->assign('arcList', $this->getNews(3));

        //       // //广告调用
        //       // $adList = $this->getAd();
        //       // $this->assign('ad',$adList);

        //       //首页左侧特价
        //       $this->assign('tj', $this->tejia(2));

        //       //友链调用
        //       $linkList = M('link')->where('is_del=0 and is_show=1 and cate_id=1')->order('ordid desc')->select();
        //       $this->assign('linkList', $linkList);
        //   }
        //   //var_dump($lineCateList[0]);
        //   $this->assign('isIndex', 1);

        $this->display();
    }

    public function index2()
    {
        //判断是否移动端
        if (isPhone()) {
            //if(0){
            header("Location: http://m.33ly.com");
        } else {
            $lineMod = M('goods');

            //首页幻灯调用
            $bannerList = M('banner')->where('is_show=1 and is_del=0 and pid=0')->order('ordid desc')->limit(5)->select();
            foreach ($bannerList as $key => $value) {
                $bannerList[$key]['son'] = M('banner')->where('is_show=1 and is_del=0 and pid=' . $value['id'])->limit(3)->select();
            }
            $this->assign('bannerList', $bannerList);

            //楼层调用（延迟加载）
            $this->assign("cjList", $this->getLinesByTypeId(3));
            $this->assign("gnList", $this->getLinesByTypeId(2));
            $this->assign("zbList", $this->getLinesByTypeId(1));

            //推荐调用

            //当季热推
            $hotMod = new Model();
            $sql = "select *
			from 33_goods as g
			where g.is_del=0 and g.is_hot=1 and g.minprice<>0
			order by g.ordid desc,g.add_time desc
			limit 0,5";
            $hotList = $hotMod->query($sql);
            //$hotList = $lineMod->where('is_del=0 and is_hot=1')->limit(5)->select();
            foreach ($hotList as $key => $value) {
                $hotList[$key]['info'] = msubstr(strip_tags($value['info']), 45);
                $hotList[$key]['pinyin'] = M('goods_cate')->where('id=' . $value['type_id'])->getfield('pinyin');
                $hotList[$key]['pinyin'] = str_replace("you", "", $hotList[$key]['pinyin']);
                $hotList[$key]['switch'] = json_decode($value['switch']);
            }
            $this->assign('hotList', $hotList);

            //文章调用
            // $arcList = M('article')->where('cate_id=1 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->limit(5)->select();
            // $this->assign('arcList',$arcList);
            $this->assign('arcList', $this->getNews(3));

            // //广告调用
            // $adList = $this->getAd();
            // $this->assign('ad',$adList);

            //首页左侧特价
            $tj = M('goods')->where('is_del=0 and is_show=1 and switch like "%2%"')->order('ordid desc')->limit(2)->select();
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
                }
            }
            $this->assign('tj', $tj);

            //友链调用
            $linkList = M('link')->where('is_del=0 and is_show=1')->order('ordid desc')->select();
            $this->assign('linkList', $linkList);
        }
        //var_dump($lineCateList[1]);
        $this->assign('isIndex', 1);

        $this->display();
    }

    public function search()
    {
        $mod = M('goods');
        $where['33_goods.is_del'] = 0;
        $map_org['33_goods.is_del'] = 0;
        $where['33_goods.is_show'] = 1;
        $map_org['33_goods.is_show'] = 1;
        $where['type_id'] = array('neq', 97);
        $map_org['type_id'] = array('neq', 97);
        $price = $_REQUEST['price'];
        switch ($price) {
            case 1:
                $map['minprice'] = array('between', '1,1000');
                break;
            case 2:
                $map['minprice'] = array('between', '1000,2000');
                break;
            case 3:
                $map['minprice'] = array('between', '2000,5000');
                break;
            case 4:
                $map['minprice'] = array('egt', 5000);
                break;
            default:
                break;
        }

        //敏感词-》跳转苏州游
        if ($this->isFilterWord($_REQUEST['keyword'])) {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 not found");
            //exit();
            //include("http://www.33ly.com/suzhou/");
            header("location: http://www.33ly.com/suzhou/");
        }

        $param = '';
        $searchKeyword = M('ad_search')->where('id=1')->find();
        if ($_REQUEST['keyword'] == $searchKeyword['name']) {
            //$searchKeyword = M('ad_search')->where('id=1')->getfield('link');
            header("location: " . $searchKeyword['link']);
        }

        if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
            $keyword = htmlspecialchars(trim($_REQUEST['keyword']));
            //$isCate = M('goods_cate')->where('name="'.$keyword.'"')->getfield('id');
            //$where .= " AND name LIKE '%".$keyword."%'";
            //if($isCate){
            //    $where['cate_id'] = $isCate;
            //    $map_org['cate_id']    = $isCate;
            //}else{
            $where['33_goods.name|33_goods.subname|33_goods.seo_title'] = array('like', '%' . $keyword . '%');
            $map_org['33_goods.name|33_goods.subname|33_goods.seo_title'] = array('like', '%' . $keyword . '%');
            //}
            $this->assign('keyword', $keyword);
            $param .= 'keyword=' . $keyword;
        }
        if (isset($_REQUEST['days']) && trim($_REQUEST['days'])) {
            $days = trim($_REQUEST['days']);
            //$where .= " AND days = ".$days."%'";
            $where['days'] = $days;
            $this->assign('days', $days);
            $param .= 'days=' . $days;
        }
        if (isset($_REQUEST['minprice']) && trim($_REQUEST['minprice'])) {
            $minprice = trim($_REQUEST['minprice']);
            if (!$minprice) {
                $minprice = 1;
            }

            //$where .= " AND price>='".$minprice."'";
            $where['minprice'] = array('ngt', $minprice);
            $this->assign('minprice', $minprice);
            $param .= 'minprice=' . $minprice;
        } else {
            $where['minprice'] = array('neq', 0);
        }
        if (isset($_REQUEST['maxprice']) && trim($_REQUEST['maxprice'])) {
            $maxprice = trim($_REQUEST['maxprice']);
            //$where .= " AND price>'".$maxprice."'";
            $where['maxprice'] = array('lt', $maxprice);
            $param .= 'maxprice=' . $maxprice;
        }

        $dayList = $mod->field('33_goods.*,c.id as cate_id,c.name as cate_name')->join('33_goods_cate as c on c.id=33_goods.cate_id')->where($map_org)->group('days')->select();
        $this->assign('dayList', $dayList);
        //价格区间
        $this->assign('price', C('ss_range'));

        //ajax分页
        import("@.ORG.Page");
        $list = $mod->field('33_goods.*,c.id as cate_id,c.name as cate_name')->join('33_goods_cate as c on c.id=33_goods.cate_id')->where($where)->order('33_goods.ordid desc,add_time desc')->select();
        $count_num = $mod->field('33_goods.*,c.id as cate_id,c.name as cate_name')->join('33_goods_cate as c on c.id=33_goods.cate_id')->where($where)->count();
        $zzt_num = 0;
        $this->assign('count_num', $count_num);
        foreach ($list as $key => $value) {
            $list[$key]['info'] = msubstr(strip_tags($value['info']), 80);
            //$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
            $list[$key]['dep'] = $this->getDeparture($value['id'], 1);
            $list[$key]['switch'] = json_decode($value['switch']);
            if (stripos($value['switch'], '1') or ($value['brand_id'] == 1)) {
                $list[$key]['is_zzt'] = 1;
                $zzt_num++;
            }
            switch ($value['type_id']) {
                case 1:
                    $list[$key]['mtype'] = 'zhoubian';
                    break;
                case 2:
                    $list[$key]['mtype'] = 'guonei';
                    break;
                case 3:
                    $list[$key]['mtype'] = 'chujing';
                    break;
                case 326:
                    $list[$key]['mtype'] = 'suzhou';
                    break;
            }
        }
        $this->assign('zzt_num', $zzt_num);
        $param = array(
            'result' => $list, //分页用的数组或sql
            'listvar' => 'list', //分页循环变量
            'listRows' => 8, //每页记录数
            //'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
            'parameter' => '',
            'target' => 'content', //ajax更新内容的容器id，不带#
            'pagesId' => 'page', //分页后页的容器id不带# target和pagesId同时定义才Ajax分页
            'template' => 'Index:ajaxlist', //ajax更新模板
        );
        $this->page($param);
        $this->display();
    }

    private function isFilterWord($param)
    {
        $words = explode('|', C('filterWord'));
        $flag = 0;
        foreach ($words as $key => $value) {
            if (strpos($param, $value) !== false) {
                $flag = $flag + 1;
            } else {
                $flag = $flag + 0;
            }
        }
        return $flag;
    }

    public function test()
    {
        echo strtotime('2016-3-31') . '<br>';
        echo strtotime('2016-4-31');
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

    public function getLinesByTypeId($type_id)
    {
        $linesMod = new Model();
        $sql = "select *
			from 33_goods as g
			where g.is_del=0 and g.is_show=1 and g.minprice<>0 and g.type_id = " . $type_id . "
			order by g.ordid desc,g.add_time desc
			limit 0,11";
        $linesList = $linesMod->order('ordid desc')->query($sql);
        return $linesList;
    }

    public function chujing()
    {
        // $adList = $this->getAd();//广告调用（照片墙）
        // $this->assign('ad',$adList);
        //出境热销线路调用
        $mod = new Model();
        $sql = "select *
			from 33_goods as g
			where g.minprice<>0 and g.is_del=0 and g.is_show=1 and g.type_id = 3
			order by g.ordid desc
			limit 0,13";
        $hotList = $mod->query($sql);
        $hotList[0]['info'] = msubstr(strip_tags($hotList[0]['info']), 100);
        $this->assign('hotList', $hotList);

        $this->assign('nav', '出境游');
        $this->display();
    }

    public function guonei()
    {
        $cate_mod = M('goods_cate');
        $hotCate = $cate_mod->where('pid=2 and is_del=0 and is_auto=0')->order('ordid desc')->limit(6)->select();
        foreach ($hotCate as $key => $value) {
            $res = $this->getLinesById($value['id'], 5);
            foreach ($res as $skey => $svalue) {
                $res[$skey]['info'] = msubstr(strip_tags($svalue['info']), 50);
            }
            $hotCate[$key]['son'] = $res;
        }
        $this->assign('hotCate', $hotCate);

        //banner
        $bannerList = M('banner_other')->where('pid=1 and is_del=0 and is_show=1')->order('ordid desc')->limit(10)->select();
        $this->assign('bannerList', $bannerList);

        $this->assign('nav', '国内游');
        $this->display();
    }

    public function zhoubianbak()
    {
        //1,2,3,4,5日
        $lineMod = M('goods');
        $lineList = array();
        $lineList[0]['lines'] = $lineMod->field('id,name,subname,market_price,minprice,info')->where('type_id=1 and days=1 and is_del=0 and is_show=1 and minprice <>0')->order('ordid desc')->limit(6)->select();
        $lineList[1]['lines'] = $lineMod->field('id,name,subname,market_price,minprice,info')->where('type_id=1 and days=2 and is_del=0 and is_show=1 and minprice <>0')->order('ordid desc')->limit(6)->select();
        $lineList[2]['lines'] = $lineMod->field('id,name,subname,market_price,minprice,info')->where('type_id=1 and days=3 and is_del=0 and is_show=1 and minprice <>0')->order('ordid desc')->limit(6)->select();
        $lineList[3]['lines'] = $lineMod->field('id,name,subname,market_price,minprice,info')->where('type_id=1 and days=4 and is_del=0 and is_show=1 and minprice <>0')->order('ordid desc')->limit(6)->select();
        $lineList[4]['lines'] = $lineMod->field('id,name,subname,market_price,minprice,info')->where('type_id=1 and days=5 and is_del=0 and is_show=1 and minprice <>0')->order('ordid desc')->limit(6)->select();
        foreach ($lineList as $key => $value) {
            $lineList[$key]['sday'] = $key + 1;
            foreach ($value['lines'] as $skey => $svalue) {
                $lineList[$key]['lines'][$skey]['info'] = msubstr(strip_tags($svalue['info']), 100);
            }
        }
        $this->assign('lineList', $lineList);

        //hotLines
        $hotLines = $lineMod->where('type_id=1 and is_del=0 and minprice<>0')->order('ordid desc,add_time desc')->limit(10)->select();
        $this->assign('hotLines', $hotLines);

        //banner
        $bannerList = M('banner_other')->where('pid=2 and is_del=0 and is_show=1')->order('ordid desc')->limit(10)->select();
        $this->assign('bannerList', $bannerList);

        // //广告调用
        // $adList = $this->getAd();
        // $this->assign('ad',$adList);

        $this->assign('nav', '周边游');
        $this->display();
    }

    public function zhoubian()
    {
        //初始化要显示的分类
        $cateArr = array(
            5 => array(
                'id' => 5,
                'pinyin' => 'zhejiang',
                'name' => '浙江',
            ),
            6 => array(
                'id' => 6,
                'pinyin' => 'jiangsu',
                'name' => '江苏',
            ),
            10 => array(
                'id' => 10,
                'pinyin' => 'anhui',
                'name' => '安徽',
            ),
            410 => array(
                'id' => 410,
                'pinyin' => 'shanlingshuixiu',
                'name' => '山灵水秀',
            ),
            411 => array(
                'id' => 411,
                'pinyin' => 'shuixiangguzhen',
                'name' => '水乡古镇',
            ),
            412 => array(
                'id' => 412,
                'pinyin' => 'dushimingcheng',
                'name' => '都市名城',
            ),
        );

        //短线线路
        $lineArr = M('goods as g')
            ->field('g.id,g.name,g.subname,g.info,g.imgurl,g.minprice')
            ->where('g.type_id=1 and g.is_del=0 and g.is_show=1 and g.minprice<>0')
            ->select();
        foreach ($cateArr as $key => $value) {
            //查找层级
            $cateInfo = M('goods_cate')->where('id="' . $value['id'] . '"')->find();
            if ($cateInfo['floor'] == 2 and $cateInfo['is_end'] == 0) {
                $cateList = M('goods_cate')->where('pid="' . $value['id'] . '" and is_del=0')->order('ordid desc')->getfield('id', true);
                $map['cate_id'] = array('in', $cateList);
                $goods_ids = M('goods_cate_rela')->where($map)->group('goods_id')->getfield('goods_id', true);
                unset($map);
            }
            if ($cateInfo['floor'] == 3 or ($cateInfo['floor'] == 2 and $cateInfo['is_end'] == 1)) {
                $goods_ids = M('goods_cate_rela')->where('cate_id="' . $value['id'] . '"')->getfield('goods_id', true);
            }
            foreach ($lineArr as $skey => $svalue) {
                if (in_array($svalue['id'], $goods_ids)) {
                    $cateArr[$key]['son'][] = $svalue;
                }
            }
        }
        $this->assign('linesArr', $cateArr);

        //新品3条
        $new = M('goods')->where('type_id=1 and is_show=1 and is_del=0 and minprice<>0')->order('add_time desc')->limit(3)->select();
        $this->assign('new', $new);

        //热门7条
        $hot = M('goods')->where('type_id=1 and is_show=1 and is_del=0 and minprice<>0')->order('ordid desc')->limit(7)->select();
        $this->assign('hot', $hot);

        //banner
        $bannerList = M('banner_other')->where('pid=2 and is_del=0 and is_show=1')->order('ordid desc')->limit(10)->select();
        $this->assign('bannerList', $bannerList);

        $this->display();
    }

    //功能函数，判断二维数组是否存在
    public function in2array($string, $arr)
    {
        $exist = false;
        foreach ($arr as $value) {
            if (in_array($string, $value)) {
                $exist = true;
                break;
            }
        }
        return $exist;
    }

    public function youlun()
    {
        $nowtime = time();

        //幻灯
        $bannerList = M('banner_other')->where('pid=4 and is_show=1 and is_del=0')->order('ordid desc')->select();
        $this->assign('bannerList', $bannerList);

        //沪上特价
        $salelist = M('ship as s')->field('s.*,b.imgurl as bimg')->join('33_ship_boat as b on b.id=s.boat_id')->where('s.is_show=1 and s.is_hot=1 and s.is_del=0 and s.minprice <>0 and s.start_time > ' . $nowtime)->order('s.ordid desc,s.start_time')->limit(15)->select();
        $this->assign('salelist', $salelist);

        //沪上热门
        $hotList = M('ship')->where('is_show=1 and is_del=0 and minprice <>0 and start_time > ' . $nowtime)->order('start_time')->select();
        $firstTime = $hotList[0]['start_time'] ? $hotList[0]['start_time'] : time();
        $nowYear = date('Y', $firstTime); //当前月份
        $nowMonth = date('m', $firstTime); //当前月份
        $nowTime = strtotime($nowYear . '-' . $nowMonth);
        $this->assign('nowTime', $nowTime);

        for ($i = 0; $i < 12; $i++) {
            if (($nowMonth + $i) > 12) {
                $newYear = $nowYear + 1;
                $newMonth = $nowMonth + $i - 12;
            } else {
                $newYear = $nowYear;
                $newMonth = $nowMonth + $i;
            }
            $key = strtotime($newYear . '-' . $newMonth);
            if ($i < 6) {
                $startTime = $key;
                if ($newMonth + 1 > 12) {
                    $newYear = $newYear + 1;
                    $newMonth = 1;
                }
                $endTime = strtotime($newYear . '-' . ($newMonth + 1));
                //echo date('Y-m-d H:i:s',$startTime);
                //echo "<br>";
                //echo date('Y-m-d H:i:s',$endTime);
                $dateList[$key]['son'] = M('ship')->where('is_show=1 and is_del=0 and minprice <>0 and start_time >= ' . $startTime . ' and start_time < ' . $endTime)->order('ordid desc,start_time')->limit(5)->select();
                //echo M('ship')->getlastsql();
            } else {
                $dateList[$key] = null;
            }
        }
        $this->assign('dateList', $dateList);

        //船体信息
        $boatList = M('ship_boat as b')->join('33_ship_company as c on c.id=b.company_id')->field('b.*,c.imgurl as cimg')->where('b.is_del=0 and b.is_show=1')->order('ordid desc')->select();
        $this->assign('boatList', $boatList);

        $this->display();
    }

    public function qianzheng()
    {
        $vkey = "29524c27-42e6-4981-a0d0-7259e0caa364";
        $vdata = $_REQUEST['date'];
        $vdftime = $_REQUEST['dftime'];
        $vsign = $_REQUEST['sign'];
        $exptime = $vdata + 60 * $vdftime;
        if ($exptime < time()) {
            //echo $vdata."<br>";
            //echo $vdftime."<br>";
            //echo $exptime."<br>";
            //            echo "过期";
            session('login_from', null);
        } else {
//            echo 'sign:'.$vsign.'<br>';
            //            echo 'data:'.$vdata.'<br>';
            //            echo 'dftime:'.$vdftime.'<br>';

            if ($vsign == md5($vdata . $vkey . $vdftime)) {
//                echo 'nsign:'.md5($vdata.$vkey.$vdftime).'<br>';
                //                echo '成功';
                session('login_from', 'erp');
            } else {
//                echo 'nsign:'.md5($vdata.$vkey.$vdftime).'<br>';
            }
        }

        $mod = M('visa');
        $cateMod = M('visa_cate');
        //热推
        $hotList = $mod
            ->field('33_visa.id,33_visa.name,33_visa.price,c.imgurl')
            ->join('33_visa_cate as c ON 33_visa.cate_id = c.id')
            ->where('33_visa.is_del=0 and 33_visa.is_hot=1')
            ->order('33_visa.ordid desc')
            ->limit(12)
            ->select();
        $this->assign('hotList', $hotList);

        //分类
        $cateList = $cateMod->where('floor=1 and is_del=0')->select();
        foreach ($cateList as $key => $value) {
            $res = $cateMod->where('pid=' . $value['id'] . ' and is_del=0')->order('ordid desc')->select();
            $cateList[$key]['son'] = $res;
        }
        $this->assign('cateList', $cateList);

        //签证问题
        $qList = M('article')->where('cate_id=4 and is_del=0')->select();
        $this->assign('qList', $qList);

        //使馆咨询
        $sList = M('article')->where('cate_id=5 and is_del=0')->limit(10)->select();
        $this->assign('sList', $sList);

        $this->assign('nav', '签证');
        $this->display();
    }

    public function ziyouxing()
    {
        $bannerList = M('banner_other')->where('pid=5 and is_del=0 and is_show=1')->select();
        $this->assign('bannerList', $bannerList);

        $this->assign('zbFreeCate', $this->getFreeCate(1));
        $this->assign('gnFreeCate', $this->getFreeCate(2));
        $this->assign('cjFreeCate', $this->getFreeCate(3));
        $this->assign('zbFreeLine', $this->getFreeLine(1));
        $this->assign('gnFreeLine', $this->getFreeLine(2));
        $this->assign('cjFreeLine', $this->getFreeLine(3));

        //热销
        $hotList = M('goods')->where('is_del=0 and is_show=1 and is_hot=1 and is_zyx=1 and minprice<>0')->order('ordid desc')->limit(3)->select();
        foreach ($hotList as $key => $value) {
            switch ($value['type_id']) {
                case 1:
                    $hotList[$key]['pinyin'] = 'zhoubian';
                    break;
                case 2:
                    $hotList[$key]['pinyin'] = 'guonei';
                    break;
                case 3:
                    $hotList[$key]['pinyin'] = 'chujing';
                    break;
            }
        }
        $this->assign('hotList', $hotList);

        $this->display();
    }

    public function tuandui()
    {
        $bannerList = M('banner_other')->where('pid=3 and is_del=0 and is_show=1')->select();
        $this->assign('bannerList', $bannerList);

        $slist = M('goods')->where('(brand_id=1 or brand_id=3) and is_show=1 and is_del=0 and minprice<>0')->order('ordid desc,add_time desc')->select();
        foreach ($slist as $key => $value) {
            $slist[$key]['scenic'] = M('trip')->field('scene')->where('pid=' . $value['id'])->limit(3)->select();
            $sindex = 0;
            $slist[$key]['scenicall'] = '';
            foreach ($slist[$key]['scenic'] as $skey => $svalue) {
                if (!$sindex) {
                    $sindex++;
                    $slist[$key]['scenicall'] .= $svalue['scene'];
                } else {
                    $slist[$key]['scenicall'] .= ',' . $svalue['scene'];
                }
            }
            $slist[$key]['dep'] = $this->getDeparture($value['id'], 1);
            switch ($value['type_id']) {
                case 1:
                    $slist[$key]['pinyin'] = 'zhoubian';
                    break;
                case 2:
                    $slist[$key]['pinyin'] = 'guonei';
                    break;
                case 3:
                    $slist[$key]['pinyin'] = 'chujing';
                    break;
            }
        }
        $this->assign('slist', $slist);

        //1天汇总
        $daylist1 = M('goods')->where('type_id = 97 and days=1 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
        foreach ($daylist1 as $key => $value) {
            $daylist1[$key]['scenic'] = M('trip')->field('scene')->where('pid=' . $value['id'])->limit(3)->select();
            $sindex = 0;
            $daylist1[$key]['scenicall'] = '';
            foreach ($daylist1[$key]['scenic'] as $skey => $svalue) {
                if (!$sindex) {
                    $sindex++;
                    $daylist1[$key]['scenicall'] .= $svalue['scene'];
                } else {
                    $daylist1[$key]['scenicall'] .= ',' . $svalue['scene'];
                }
            }
            $daylist1[$key]['switch'] = json_decode($value['switch']);
        }
        $this->assign('daylist1', $daylist1);

        //2天汇总
        $daylist2 = M('goods')->where('type_id = 97 and days=2 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
        foreach ($daylist2 as $key => $value) {
            $daylist2[$key]['scenic'] = M('trip')->field('scene')->where('pid=' . $value['id'])->limit(3)->select();
            $sindex = 0;
            $daylist2[$key]['scenicall'] = '';
            foreach ($daylist2[$key]['scenic'] as $skey => $svalue) {
                if (!$sindex) {
                    $sindex++;
                    $daylist2[$key]['scenicall'] .= $svalue['scene'];
                } else {
                    $daylist2[$key]['scenicall'] .= ',' . $svalue['scene'];
                }
            }
            $daylist2[$key]['switch'] = json_decode($value['switch']);
        }
        $this->assign('daylist2', $daylist2);

        //3天汇总
        $daylist3 = M('goods')->where('type_id = 97 and days=3 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
        foreach ($daylist3 as $key => $value) {
            $daylist3[$key]['scenic'] = M('trip')->field('scene')->where('pid=' . $value['id'])->limit(3)->select();
            $sindex = 0;
            $daylist3[$key]['scenicall'] = '';
            foreach ($daylist3[$key]['scenic'] as $skey => $svalue) {
                if (!$sindex) {
                    $sindex++;
                    $daylist3[$key]['scenicall'] .= $svalue['scene'];
                } else {
                    $daylist3[$key]['scenicall'] .= ',' . $svalue['scene'];
                }
            }
            $daylist3[$key]['switch'] = json_decode($value['switch']);
        }
        $this->assign('daylist3', $daylist3);

        //高铁汇总
        $daylist4 = M('goods')->where('type_id = 97 and (name like "%双高%" or subname like "%双高%" or name like "%双动%" or subname like "%双动%") and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
        foreach ($daylist4 as $key => $value) {
            $daylist4[$key]['scenic'] = M('trip')->field('scene')->where('pid=' . $value['id'])->limit(3)->select();
            $sindex = 0;
            $daylist4[$key]['scenicall'] = '';
            foreach ($daylist4[$key]['scenic'] as $skey => $svalue) {
                if (!$sindex) {
                    $sindex++;
                    $daylist4[$key]['scenicall'] .= $svalue['scene'];
                } else {
                    $daylist4[$key]['scenicall'] .= ',' . $svalue['scene'];
                }
            }
            $daylist4[$key]['switch'] = json_decode($value['switch']);
        }
        $this->assign('daylist4', $daylist4);
        $this->display();
    }

    public function nextPage()
    {
        $start = $_POST['start'];
        $start = $start + 5;
        $hotMod = new Model();
        $sql = "select *
			from 33_goods as g
			where g.is_del=0 and g.is_hot=1 and minprice<>0
			order by g.ordid desc,g.add_time desc
			limit " . $start . ",5";
        $sql2 = "select *
			from 33_goods as g
			where g.is_del=0 and g.is_hot=1 and minprice<>0
			order by g.ordid desc,g.add_time desc
			limit 0,5";
        $hotList = $hotMod->query($sql);

        //if(!$hotList){
        if ($start > 10) {
            $hotList = $hotMod->query($sql2);
            $start = 0;
        }

        $html = "";
        foreach ($hotList as $key => $value) {
            $hotList[$key]['info'] = msubstr(strip_tags($value['info']), 45);
            $hotList[$key]['pinyin'] = M('goods_cate')->where('id=' . $value['type_id'])->getfield('pinyin');
            $hotList[$key]['pinyin'] = str_replace("you", "", $hotList[$key]['pinyin']);
            $hotList[$key]['switch'] = json_decode($value['switch']);
            $html .= "<li><a href='" . __ROOT__ . "/" . $hotList[$key]['pinyin'] . "/xianlu-" . $value['id'] . ".html'><dl><dt>";
            $html .= "<p class='h_n_name'><span class='h_n_link'>
			          <" . $value['name'] . "></span><span class='h_n_shortdes'>" . $value['subname'] . "</span>";

            //尾部标签
            //if($value['switch'][0]){
            //    $html .= "<span class='mark'>自组团</span>";
            //}

            $html .= "</p><p class='h_n_description'>" . $hotList[$key]['info'] . "</p></dt>";
            $html .= "<dd><p class='h_price'>￥<span class='price'>" . isNull($value['minprice']) . "</span>起</p>";
            $html .= "</dd></dl></a></li>";
        }
        //$html = json_encode($hotList);
        //拼接HTML
        //do sth.
        $this->ajaxReturn($html, $start, 1);
    }

    //获取产品的最低价格
    public function getMinprice()
    {
        $goodsMod = M('goods');
        $shipMod = M('ship');
        $goodsTimeMod = M('departure_time');
        //$shipTimeMod = M('ship_departure');
        $goodsList = $goodsMod->where('is_del=0')->select();
        //$shipList = $shipMod->where('is_del=0')->select();
        $nowTime = time();
        foreach ($goodsList as $key => $value) {
            if ($value['type_id'] == 97) {
                $minprice = $value['market_price'];
            } else {
                $minprice = $goodsTimeMod->where('pid=' . $value['id'] . ' and is_del=0 and departure_time > ' . $nowTime)->min('price');
            }
            $goodsMod->where('id=' . $value['id'])->setfield('minprice', $minprice);
            // if($value['id'] == 138){
            // echo $value['name'];
            // $minprice = $goodsTimeMod->where('pid='.$value['id'].' and departure_time > '.$nowTime)->min('price');
            // echo $goodsTimeMod->getlastsql();
            // echo $minprice;
            // }
        }
    }

    //自由行-获得分类
    private function getFreeCate($tid)
    {
        $mod = M('goods as g');
        $map['g.is_del'] = 0;
        $map['g.is_show'] = 1;
        $map['gc.is_auto'] = 0;
        $map['g.minprice'] = array('neq', 0);
        $map['g.is_zyx'] = 1;
        $map['g.type_id'] = $tid;
        $cateList = $mod->field('g.cate_id as cate,gc.name,gc.pinyin as pinyin,count(g.id) as num')->join('33_goods_cate as gc on gc.id=g.cate_id')->where($map)->order('g.ordid desc')->group('g.cate_id')->select();
        return $cateList;
    }

    //自由行-获得产品
    private function getFreeLine($tid)
    {
        $mod = M('goods');
        $map['is_del'] = 0;
        $map['is_show'] = 1;
        $map['GroupId'] = 0;
        $map['minprice'] = array('neq', 0);
        $map['is_zyx'] = 1;
        $map['type_id'] = $tid;
        $lineList = $mod->where($map)->order('ordid desc')->limit(9)->select();
        return $lineList;
    }

    //批量产品下架  标准：暂无行程
    public function pullOffShelves()
    {
        //日志文件路径
        $file_path = "Uploads/offlog.txt";
        $fp = fopen($file_path, "a+"); //打开文件

        $mod = M('goods');
        $list = $mod->where('is_del=0 and is_show=1 and type_id<> 97')->select();
        $count = $mod->where('is_del=0 and is_show=1 and type_id<> 97')->count();
        //echo date('Y-m-d',time())."   现存".$count."条线路,下架：<br>";
        $con1 = date('Y-m-d', time()) . "   现存" . $count . "条线路,下架：\n";
        fwrite($fp, $con1);
        foreach ($list as $key => $value) {
            $dep = $this->getDeparture($value['id'], $value['sign_up']);
            //循环插入下架行程
            if (!$dep) {
                $mod->where('id=' . $value['id'])->setfield('is_show', 0);
                //echo "线路编号".$value['id']."：<".$value['name'].">".$value['subname']."<br>";
                $con2 = "线路编号" . $value['id'] . "：<" . $value['name'] . ">" . $value['subname'] . "\n";
                fwrite($fp, $con2);
            }
        }
        $con3 = "-------------------------------\n\n"; //分隔线
        fwrite($fp, $con3);
        fclose($fp); //关闭文件读写
        // $list[0]['dep'] = $this->getDeparture($list[0]['id'],$list[0]['sign_up']);
        // var_dump($list[0]);
        // $count = $mod->where('is_del=0 and is_show=1')->count();
        // echo $count;
        //var_dump($list);
    }

    //二维码测试
    public function testqr()
    {
        vendor("phpqrcode.phpqrcode");
        $data = 'http://www.baidu.com';
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
        imagepng($qr, 'ewmlogo.png');
    }

    public function getpinyin()
    {
        $mod = M('visa_cate');
        $res = $mod->where('pinyin = ""')->select();
        foreach ($res as $value) {
            $data['pinyin'] = Pinyin($value['name']);
            $mod->where('id=' . $value['id'])->save($data);
        }
        //var_dump($res);
    }

    public function clearsession()
    {
        session(null);
        echo "Clear Session Success!";
    }

    //清理出发日期
    public function delDeparture()
    {
        $lastTime = time() - 3600 * 24;
        $count = M('departure_time')->where('departure_time < ' . $lastTime)->count();
        M('departure_time')->where('departure_time < ' . $lastTime)->delete();
        echo "共清理" . $count . "条过期出发日期";
    }

    //电商特价页
    public function jingxuan()
    {
        $cateList[1] = array('CateName' => '超值热卖', 'CateUrl' => '/jingxuan/chaozhi/');
        $cateList[2] = array('CateName' => '夏日漂流', 'CateUrl' => '/jingxuan/piaoliu/');
        $cateList[3] = array('CateName' => '每日特价', 'CateUrl' => '/jingxuan/tejia/');
        $cateList[4] = array('CateName' => '自组团', 'CateUrl' => '/jingxuan/zizhutuan/');
        $cateList[5] = array('CateName' => '大西北', 'CateUrl' => '/jingxuan/xibei/');
        //$cateList[99] = array('CateName'=>'ALL','CateUrl'=>'/jingxuan/');
        $this->assign('cateList', $cateList);
        $cateName = $_REQUEST['cate'];
        //$map['is_del'] = 0;
        //$map['is_show'] = 1;
        //$map['minprice'] = array('neq',0);
        switch ($cateName) {
            case 'chaozhi':
                $tagName = '超值热卖';
                $this->assign('cateId', 1);
                break;
            case 'piaoliu':
                $tagName = '漂流';
                $this->assign('cateId', 2);
                break;
            case 'tejia':
                $tagName = '每日特价';
                $this->assign('cateId', 3);
                break;
            case 'zizhutuan':
                $tagName = '自组团';
                $this->assign('cateId', 4);
                break;
            case 'xibei':
                $tagName = '大西北';
                $this->assign('cateId', 5);
                break;
            default:
                $tagName = '超值热卖';
                $this->assign('cateId', 1);
                break;
        }

        //$goodsList = M('goods')->where($map)->select();
        $goodsList = M('tag as t')
            ->field('g.id,g.name,g.subname,g.type_id,g.market_price,g.imgurl,g.days,g.minprice,g.info')
            ->join('33_goods_tag as gt on gt.tag_id=t.id')
            ->join('33_goods as g on g.id=gt.goods_id')
            ->where('t.name="' . $tagName . '" and g.is_del=0 and g.minprice<>0 and g.is_show=1')
            ->select();

        foreach ($goodsList as $key => $value) {
            switch ($value['type_id']) {
                case 1:
                    $goodsList[$key]['cateType'] = 'zhoubian';
                    break;
                case 2:
                    $goodsList[$key]['cateType'] = 'guonei';
                    break;
                case 3:
                    $goodsList[$key]['cateType'] = 'chujing';
                    break;
            }
        }
        $this->assign('goodsList', $goodsList);

        $this->display();
    }

}
