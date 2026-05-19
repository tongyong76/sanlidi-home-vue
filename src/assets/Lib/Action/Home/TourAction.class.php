<?php

class TourAction extends BaseAction
{
    public function index()
    {
        $pinyin = $_REQUEST['pinyin'];
        $this->assign('pinyin', $pinyin);
        $cateMod = M('goods_cate');

        $info = $cateMod->where('pinyin="' . $pinyin . '" and is_del=0')->find();
        $cid = $info['id'];
        $price = $_REQUEST['price'];
        $this->assign('cid', $cid);
        $this->assign('cateName', $info['name']);
        $this->assign('cateInfo', $info);
        $floor = $info['floor'];

        //所有三级分类的产品数，通过分类ID调用
        // $allCateList = M('goods_cate')->where('is_show=1 and is_del=0 and is_end=1')->select();
        // foreach($allCateList as $key=>$value){
        // M('goods')->where('cate_id="'.$value['id'].'"')->count();
        // }

        //列表页三种
        //type_id
        if (!$floor) {
            $where = 'is_del=0 and minprice<>0 and is_show=1';
            $map_org = 'is_del=0 and minprice<>0 and is_show=1';
        }
        if ($floor == 1) {
            $arr = $goods_cate_mod->where('is_del=0')->select();
            $cate_arr = getEndChild($arr, $cid);
            $map['cate_id'] = array('in', $cate_arr);
            $goods_ids = M('goods_cate_rela')->where($map)->group('goods_id')->getfield('goods_id', true);
            $goods_ids = implode(',', $goods_ids);
            $where = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
            $map_org = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
        }
        if ($floor == 2) {
            $cateList = $cateMod->where('pid=' . $cid . ' and is_del=0')->order('ordid desc')->getfield('id', true);
            foreach ($cateList as $key => $value) {
                $f2cateinfo = M('goods_cate')->where('id="' . $value . '"')->find();
                //$f2catecount = M('goods')->where('cate_id="'.$value.'" and is_del=0 and is_show=1 and minprice <>0')->count();
                $f2catecount = M('goods_cate_rela as gcr')->join('33_goods as g on g.id=gcr.goods_id')->where('gcr.cate_id="' . $value . '" and g.is_show=1 and g.is_del=0 and g.minprice<>0')->count();
                $cateList1[$key]['pinyin'] = $f2cateinfo['pinyin'];
                $cateList1[$key]['count'] = $f2catecount;
                $cateList1[$key]['name'] = $f2cateinfo['name'];
            }
            $this->assign('cateList', $cateList1);
            if ($cateList) {
                $map['cate_id'] = array('in', $cateList);
                $goods_ids = M('goods_cate_rela')->where($map)->group('goods_id')->getfield('goods_id', true);
                $goods_ids = implode(',', $goods_ids);
                $where = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
                $map_org = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
            } else {
                $map['cate_id'] = $cid;
                $goods_ids = M('goods_cate_rela')->where($map)->getfield('goods_id', true);
                $goods_ids = implode(',', $goods_ids);
                $where = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
                $map_org = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
            }
            $this->assign('nid', $cid);
            $this->assign('allid', $cid);
            $this->assign('allpinyin', $info['pinyin']);

            //posi
            $posi = $cateMod->where('id=' . $cid)->getfield('pid');
            switch ($posi) {
                case 1:
                    $this->assign('cateType', 'zhoubian');
                    $this->assign('posi', "<a href='http://www.33ly.com/zhoubian/'>周边游</a><b>></b><span>" . $info['name'] . "</span>");
                    //处理price
                    $this->assign('price', C('zb_range'));
                    switch ($price) {
                        case 1:
                            $minprice = 0;
                            $maxprice = 100;
                            break;
                        case 2:
                            $minprice = 100;
                            $maxprice = 300;
                            break;
                        case 3:
                            $minprice = 300;
                            $maxprice = 400;
                            break;
                        case 4:
                            $minprice = 500;
                            $maxprice = 100000;
                            break;
                        default:
                            break;
                    }
                    break;
                case 2:
                    $this->assign('cateType', 'guonei');
                    $this->assign('posi', "<a href='http://www.33ly.com/guonei/'>国内游</a><b>></b><span>" . $info['name'] . "</span>");
                    $this->assign('price', C('gn_range'));
                    switch ($price) {
                        case 1:
                            $minprice = 0;
                            $maxprice = 1000;
                            break;
                        case 2:
                            $minprice = 1000;
                            $maxprice = 2000;
                            break;
                        case 3:
                            $minprice = 2000;
                            $maxprice = 5000;
                            break;
                        case 4:
                            $minprice = 5000;
                            $maxprice = 100000;
                            break;
                        default:
                            break;
                    }
                    break;
                case 3:
                    $this->assign('cateType', 'chujing');
                    $this->assign('posi', "<a href='http://www.33ly.com/chujing/'>出境游</a><b>></b><span>" . $info['name'] . "</span>");
                    $this->assign('price', C('cj_range'));
                    switch ($price) {
                        case 1:
                            $minprice = 0;
                            $maxprice = 1000;
                            break;
                        case 2:
                            $minprice = 1000;
                            $maxprice = 2000;
                            break;
                        case 3:
                            $minprice = 2000;
                            $maxprice = 5000;
                            break;
                        case 4:
                            $minprice = 5000;
                            $maxprice = 100000;
                            break;
                        default:
                            break;
                    }
                    break;
                case 97:
                    $this->assign('posi', "<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>" . $info['name'] . "</span>");
                    break;
                case 326:
                    $this->assign('posi', "<a href='http://www.33ly.com/suzhou/'>深度苏州</a><b>></b><span>" . $info['name'] . "</span>");
                    break;
            }
        }
        if ($floor == 3) {
            //cate_list并传递当前cate_id
            $cateList = $cateMod->where('pid=' . $info['pid'] . ' and is_del=0')->order('ordid desc')->getfield('id', true);
            foreach ($cateList as $key => $value) {
                $f2cateinfo = M('goods_cate')->where('id="' . $value . '"')->find();
                //$f2catecount = M('goods')->where('cate_id="'.$value.'" and is_del=0 and is_show=1 and minprice <>0')->count();
                $f2catecount = M('goods_cate_rela as gcr')->join('33_goods as g on g.id=gcr.goods_id')->where('gcr.cate_id="' . $value . '" and g.is_show=1 and g.is_del=0 and g.minprice<>0')->count();
                $cateList1[$key]['pinyin'] = $f2cateinfo['pinyin'];
                $cateList1[$key]['count'] = $f2catecount;
                $cateList1[$key]['name'] = $f2cateinfo['name'];
            }
            $this->assign('cateList', $cateList1);
            $map['cate_id'] = $cid;
            $goods_ids = M('goods_cate_rela')->where($map)->getfield('goods_id', true);
            $goods_ids = implode(',', $goods_ids);
            $where = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
            $map_org = 'is_del=0 and minprice<>0 and is_show=1 and id in (' . $goods_ids . ')';
            $this->assign('nid', $info['id']);
            $this->assign('allid', $info['pid']);
            $this->assign('allpinyin', M('goods_cate')->where('id=' . $info['pid'])->getfield('pinyin'));

            //posi
            $posi = $cateMod->where('id=' . $info['pid'])->getfield('pid');

            switch ($posi) {
                case 1:
                    $this->assign('cateType', 'zhoubian');
                    $this->assign('posi', "<a href='http://www.33ly.com/zhoubian/'>周边游</a><b>></b><span>" . $info['name'] . "</span>");
                    $this->assign('price', C('zb_range'));
                    switch ($price) {
                        case 1:
                            $minprice = 0;
                            $maxprice = 100;
                            break;
                        case 2:
                            $minprice = 100;
                            $maxprice = 300;
                            break;
                        case 3:
                            $minprice = 300;
                            $maxprice = 400;
                            break;
                        case 4:
                            $minprice = 500;
                            $maxprice = 100000;
                            break;
                        default:
                            break;
                    }
                    break;
                case 2:
                    $this->assign('cateType', 'guonei');
                    $this->assign('posi', "<a href='http://www.33ly.com/guonei/'>国内游</a><b>></b><span>" . $info['name'] . "</span>");
                    $this->assign('price', C('gn_range'));
                    switch ($price) {
                        case 1:
                            $minprice = 0;
                            $maxprice = 1000;
                            break;
                        case 2:
                            $minprice = 1000;
                            $maxprice = 2000;
                            break;
                        case 3:
                            $minprice = 2000;
                            $maxprice = 5000;
                            break;
                        case 4:
                            $minprice = 5000;
                            $maxprice = 100000;
                            break;
                        default:
                            break;
                    }
                    break;
                case 3:
                    $this->assign('cateType', 'chujing');
                    $this->assign('posi', "<a href='http://www.33ly.com/chujing/'>出境游</a><b>></b><span>" . $info['name'] . "</span>");
                    $this->assign('price', C('cj_range'));
                    switch ($price) {
                        case 1:
                            $minprice = 0;
                            $maxprice = 1000;
                            break;
                        case 2:
                            $minprice = 1000;
                            $maxprice = 2000;
                            break;
                        case 3:
                            $minprice = 2000;
                            $maxprice = 5000;
                            break;
                        case 4:
                            $minprice = 5000;
                            $maxprice = 100000;
                            break;
                        default:
                            break;
                    }
                    break;
                case 97:
                    $this->assign('posi', "<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>" . $info['name'] . "</span>");
                    break;
            }
        }

        $dayList = M('goods')->field('days')->where($map_org)->group('days')->select();
        $this->assign('dayList', $dayList);

        //筛选条件
        if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
            $keyword = trim($_REQUEST['keyword']);
            $where .= " AND name LIKE '%" . $keyword . "%'";
            $this->assign('keyword', $keyword);
        }
        if (isset($_REQUEST['cid']) && trim($_REQUEST['cid'])) {
            $cid = trim($_REQUEST['cid']);
            //    $where .= " AND cate_id = $cid ";
            $this->assign('cid', $cid);
        }
        if (isset($_REQUEST['days']) && trim($_REQUEST['days'])) {
            $days = trim($_REQUEST['days']);
            $where .= " AND days = " . $days;
            $this->assign('days', $days);
        }
        //if (isset($_REQUEST['minprice']) && trim($_REQUEST['minprice'])) {
        if ($minprice) {
            //$minprice = trim($_REQUEST['minprice']);
            $where .= " AND minprice>='" . $minprice . "'";
            $this->assign('minprice', $minprice);
        }
        //if (isset($_REQUEST['maxprice']) && trim($_REQUEST['maxprice'])) {
        if ($maxprice) {
            //$maxprice = trim($_REQUEST['maxprice']);
            $where .= " AND minprice<'" . $maxprice . "'";
            $this->assign('maxprice', $maxprice);
        }

        //ajax分页
        import("@.ORG.Page");
        $list = M('goods')->where($where)->order('ordid desc,add_time desc')->select();
        $count_num = M('goods')->where($where)->order('ordid desc,add_time desc')->count();
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
            'template' => 'Tour:ajaxlist', //ajax更新模板
        );
        $this->page($param);

        $this->display();
    }

    public function free()
    {

        $mod = M('goods as g');

        //传递筛选条件
        if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
            $keyword = trim($_REQUEST['keyword']);
            $where .= " AND name LIKE '%" . $keyword . "%'";
            $this->assign('keyword', $keyword);
        }
        if (isset($_REQUEST['cid']) && trim($_REQUEST['cid'])) {
            $cid = trim($_REQUEST['cid']);
            //    $where .= " AND cate_id = $cid ";
            $this->assign('cid', $cid);
        }
        if (isset($_REQUEST['days']) && trim($_REQUEST['days'])) {
            $days = trim($_REQUEST['days']);
            $map['g.days'] = $days;
            $this->assign('days', $days);
        }
        //if (isset($_REQUEST['minprice']) && trim($_REQUEST['minprice'])) {
        if ($minprice) {
            //$minprice = trim($_REQUEST['minprice']);
            $where .= " AND minprice>='" . $minprice . "'";
            $this->assign('minprice', $minprice);
        }
        //if (isset($_REQUEST['maxprice']) && trim($_REQUEST['maxprice'])) {
        if ($maxprice) {
            //$maxprice = trim($_REQUEST['maxprice']);
            $where .= " AND minprice<'" . $maxprice . "'";
            $this->assign('maxprice', $maxprice);
        }

        //通用条件
        if ($_REQUEST['type_id']) {
            $map['type_id'] = trim($_REQUEST['type_id']);
        }
        $map['g.is_del'] = 0;
        $map['g.is_show'] = 1;
        $map['g.minprice'] = array('neq', 0);
        $map['g.is_zyx'] = 1;

        import("@.ORG.Page");
        $map3 = $map;
        $pinyin = $_REQUEST['pinyin'];
        if ($pinyin) {
            $type_id = M('goods_cate')->where('pinyin ="' . $pinyin . '"')->getfield('id');
            $map3['g.cate_id'] = $type_id;
            $this->assign('tid', $type_id);
        } else {
            $is_all = 1;
            $this->assign('tid', 'all');
        }

        $list = $mod->where($map3)->order('g.ordid desc,g.add_time desc')->select();
        if (!$list and !$is_all) {
            header("Location: http://www.33ly.com/ziyouxing/");
        }

        $count_num = $mod->where($map3)->order('g.ordid desc,g.add_time desc')->count();
        $this->assign('count_num', $count_num);
        foreach ($list as $key => $value) {
            $list[$key]['info'] = msubstr(strip_tags($value['info']), 80);
            //$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
            $list[$key]['dep'] = $this->getDeparture($value['id'], 1);
            switch ($value['type_id']) {
                case 1:
                    $list[$key]['type'] = 'zhoubian';
                    break;
                case 2:
                    $list[$key]['type'] = 'guonei';
                    break;
                case 3:
                    $list[$key]['type'] = 'chujing';
                    break;
            }
        }

        //时间条件
        $nowTime = time();
        $nowYear = date('Y', $nowTime);
        $nowMonth = date('m', $nowTime);

        //前台筛选条件
        $condition['days'] = $mod->field('g.days,count(id) as num')->where($map)->group('g.days')->select();
        $map1 = $map;
        $map1['g.type_id'] = array('neq', 2);
        $condition['cate1'] = $mod->field('g.cate_id as cate,gc.name,count(g.id) as num')->join('33_goods_cate as gc on gc.id=g.cate_id')->where($map1)->order('g.type_id asc')->group('g.cate_id')->select();
        $map2 = $map;
        $map2['g.type_id'] = 2;
        $condition['cate2'] = $mod->field('g.cate_id as cate,gc.name,count(g.id) as num')->join('33_goods_cate as gc on gc.id=g.cate_id')->where($map2)->order('g.type_id asc')->group('g.cate_id')->select();
        for ($i = 0; $i < 6; $i++) {
            if (($nowMonth + $i) <= 12) {
                $startTime = $nowYear . ($nowMonth + $i) . '01';
            } else {
                $startTime = ($nowYear + 1) . sprintf("%02d", ($nowMonth + $i - 12)) . '01';
            }
            $condition['starttime'][$i]['starttime'] = strtotime($startTime);
            $condition['starttime'][$i]['month'] = $nowMonth + $i;
        }
        $this->assign('condition', $condition);

        $param = array(
            'result' => $list, //分页用的数组或sql
            'listvar' => 'list', //分页循环变量
            'listRows' => 8, //每页记录数
            //'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
            'parameter' => '',
            'target' => 'content', //ajax更新内容的容器id，不带#
            'pagesId' => 'page', //分页后页的容器id不带# target和pagesId同时定义才Ajax分页
            'template' => 'Tour:ajaxfreelist', //ajax更新模板
        );
        $this->page($param);

        $this->display();
    }

    public function daylist()
    {
        $days = $_REQUEST['days'];

        if ($days) {
            $this->assign('posi2', $days . '日游');
            switch ($days) {
                case 1:
                    $this->assign('posi3', '一日游');
                    break;
                case 2:
                    $this->assign('posi3', '二日游');
                    break;
                case 3:
                    $this->assign('posi3', '三日游');
                    break;
                case 4:
                    $this->assign('posi3', '四日游');
                    break;
            }
        }
        $type = $_REQUEST['type'];
        if ($type == 'zhoubian') {
            $this->assign('cateType', 'zhoubian');
            $this->assign('posi1', '<a href="__ROOT__/zhoubian/">周边游</a>');
            $where['type_id'] = 1;
        }
        if ($type == 'guonei') {
            $this->assign('cateType', 'guonei');
            $this->assign('posi1', '<a href="__ROOT__/guonei/">国内游</a>');
            $where['type_id'] = 2;
        }

        $where['days'] = $days;

        $where['is_del'] = 0;
        $where['is_show'] = 1;
        $where['minprice'] = array('neq', 0);

        $mod = M('goods');
        $list = $mod->where($where)->order('ordid desc,add_time desc')->select();
        foreach ($list as $key => $value) {
            $list[$key]['info'] = msubstr(strip_tags($value['info']), 80);
            //$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
            $list[$key]['dep'] = $this->getDeparture($value['id'], 1);
            $list[$key]['switch'] = json_decode($value['switch']);
            $list[$key]['is_zzt'] = stripos($value['switch'], '1');
        }
        $param = array(
            'result' => $list, //分页用的数组或sql
            'listvar' => 'list', //分页循环变量
            'listRows' => 8, //每页记录数
            //'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
            'parameter' => '',
            'target' => 'content', //ajax更新内容的容器id，不带#
            'pagesId' => 'page', //分页后页的容器id不带# target和pagesId同时定义才Ajax分页
            'template' => 'Tour:ajaxlist', //ajax更新模板
        );
        $this->page($param);

        $this->display();
    }

    public function getNewList()
    {

    }

    public function detail()
    {
        $this->display();
        // $id = $_REQUEST['id'];
        // if($_REQUEST['id']) M('goods')->where('id='.$_REQUEST['id'])->setInc('count');
        // $from = $_REQUEST['from'];
        // if(isPhone()){
        //     if($from){
        //         header("Location: http://m.33ly.com/Tour/detail/id/".$id.".html?from=".$from);
        //     }else{
        //         header("Location: http://m.33ly.com/Tour/detail/id/".$id.".html");
        //     }
        // }
        // $mod = M('goods');
        // $cateMod = M('goods_cate');
        // $weekarray=array("日","一","二","三","四","五","六");
        // $this->assign('week',$weekarray);

        // $info = $mod->where('id='.$id)->find();
        // $info['cateName'] = M('goods_cate')->where('id='.$info['cate_id'])->getField('name');
        // if(!$info){
        //     $this->error('非法操作！','/');
        // }
        // //自主团
        // if(stripos($info['switch'],'1') or ($info['brand_id'] == 1)){
        //     $info['is_zzt'] = 1;
        // }

        // $info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
        // //$info['dep'] = $this->getDeparture($info['id'],1);

        // //最近行程
        // $nowtime = strtotime(date(Ymd));
        // $exptime = $nowtime + 3600*24*1;

        // $map['cid'] = $id;
        // $firstDep = M('departure_time')->where('pid='.$id.' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->find();
        // $info['startYear'] = date('Y',$firstDep['departure_time']);
        // $info['startMonth'] = date('m',$firstDep['departure_time']);

        // $info['service'] = json_decode($info['service']);
        // $info['seo_desc_common'] = msubstr(strip_tags($info['info']),45);

        // $floor = M('goods_cate')->where('id='.$info['cate_id'])->getfield('floor');

        // //浏览记录
        // $recData['goods_id'] = $id;
        // $recData['user_id'] = cookie('id');
        // $recData['add_time'] = time();
        // if($recData['user_id']){
        //     $recExistId = M('goods_record')->where(array('goods_id'=>$recData['goods_id'],'user_id'=>$recData['user_id']))->getfield('record_id');
        //     if(empty($recExistId)){
        //         M('goods_record')->add($recData);
        //     }else{
        //         M('goods_record')->where(array('record_id'=>$recExistId))->save($recData);
        //     }
        // }

        // //面包屑
        // $pidInfo = $cateMod->where('id='.$info['cate_id'])->find();
        // $ppid = $cateMod->where('id='.$pidInfo['pid'])->getfield('pid');
        // $key = $ppid?$ppid:$pidInfo['pid'];
        // switch($key){
        //     case 1:
        //         $this->assign('posi',"<a href='".__ROOT__."/zhoubian/'>周边游</a><b>></b><a href='".__ROOT__."/zhoubian/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>");
        //         $deptime = $nowtime + 3600*24*30;
        //         $depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
        //         $this->assign('depList',$depList);
        //         break;
        //     case 2:
        //         $this->assign('posi',"<a href='".__ROOT__."/guonei/'>国内游</a><b>></b><a href='".__ROOT__."/guonei/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>");
        //         $deptime = $nowtime + 3600*24*90;
        //         $depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
        //         $this->assign('depList',$depList);
        //         break;
        //     case 3:
        //         $this->assign('posi',"<a href='".__ROOT__."/chujing/'>出境游</a><b>></b><a href='".__ROOT__."/chujing/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>");
        //         $deptime = $nowtime + 3600*24*90;
        //         $depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
        //         $this->assign('depList',$depList);
        //         break;
        //     case 97:
        //         $this->assign('posi',"<a href='".__ROOT__."/tuandui/'>团队游</a><b>></b><a href='".__ROOT__."/tuandui/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>");
        //         break;
        //     case 326:
        //         $this->assign('posi',"<a href='".__ROOT__."/suzhou/'>深度苏州</a><b>></b><a href='".__ROOT__."/suzhou/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>");
        //         break;
        // }

        // //获取行程
        // $trip = M('trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
        // foreach($trip as $key=>$value){
        //     $trip[$key]['dinner'] = json_decode($value['dinner']);
        //     $scene = explode(',',$value['scene']);
        //     foreach($scene as $skey=>$svalue){
        //         if($svalue && $skey<3){
        //             $res = M('scenic')->where('name="'.$svalue.'" and is_del=0')->find();
        //             $trip[$key]['scenic'][$skey] = $res;
        //         }
        //     }

        //     //获取景点
        //     //$trip[$key]['scene'] = explode(',',$value['scene']);
        // }
        // $this->assign('trip',$trip);

        // //相关线路 大分类下其他线路or同关键词线路or同分类
        // //rList1为同小分类下其他线路，rList2为大分类下其他线路
        // $rList1 = M('goods')->where('cate_id='.$info['cate_id'].' and is_del=0 and is_show=1 and minprice<>0 and id !='.$id)->order('ordid desc,add_time desc')->limit(4)->select();
        // foreach($rList1 as $key=>$value){
        //     $catepinyin = M('goods_cate')->where('id='.$value['type_id'])->getfield('pinyin');
        //     $rList1[$key]['catepinyin'] = str_replace('you','',$catepinyin);
        // }
        // //$rList2 = M('goods')->where('type_id='.$info['type_id'].' and is_del=0')->limit(4)->select();
        // //$rList = array_merge($rList1,$rList2);
        // //$rList = explode(array_unique(implode(',',$rList)));
        // //var_dump($rList);
        // $this->assign('relative',$rList1);

        // //生成二维码
        // $urlData = str_replace('/index.php','',$_SERVER['PHP_SELF']); //替换掉index.php
        // $curl =  "http://".$_SERVER ['HTTP_HOST'].$urlData;
        // //$curl = 'http://www.33ly.com/chujing/xianlu-1087.html';
        // $ewmurl = $this->erweima($curl,$info['sn'].'V1026');
        // //echo $ewmurl;
        // $this->assign('ewmurl',$ewmurl);

        // if($info['is_ds']){
        //     $this->assign('info',$info);
        //     //$this->display('detail_ds');
        //     $this->display();
        // }else{
        //     if($info['is_zyx'] and ($info['type_id'] <> 1 or $info['tpl_id'] == 1)){
        //     //if(0){
        //         //交通信息
        //         $traffic = M('goods_flight')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
        //         $this->assign('traffic',$traffic);
        //         //住宿信息
        //         $hotel = M('goods_hotel as gh')->join('33_hotel as h on h.hotel_id=gh.hotel_id')->where('gh.pid='.$info['id'].' and gh.is_del=0')->order('gh.ordid')->select();
        //         foreach($hotel as $key=>$value){
        //             $hotel[$key]['img_list'] = M('hotel_gallery')->where('hotel_id='.$value['hotel_id'])->select();
        //         }
        //         $this->assign('hotel',$hotel);
        //         $this->assign('info',$info);
        //         $this->display('ziyouxing');
        //     }else{
        //         $this->assign('info',$info);
        //         $this->display();
        //     }
        // }
    }

    public function online()
    {
        $id = $_REQUEST['id'];
        $from = $_REQUEST['from'];
        if (isPhone()) { //判断设备，手机跳转
            // if($from){
            // header("Location: http://m.33ly.com/Tour/detail/id/".$id.".html?from=".$from);
            // }else{
            // header("Location: http://m.33ly.com/Tour/detail/id/".$id.".html");
            // }
        }
        $mod = M('goods');
        $cateMod = M('goods_cate');
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        $this->assign('week', $weekarray);
        $info = $mod->where('id=' . $id)->find();
        if (!$info) {
            $this->error('非法操作！', '/');
        }
        //自主团
        if (stripos($info['switch'], '1') or ($info['brand_id'] == 1)) {
            $info['is_zzt'] = 1;
        }

        $info['dep'] = $this->getDeparture($info['id'], $info['sign_up']);
        //$info['dep'] = $this->getDeparture($info['id'],1);

        //最近行程
        $nowtime = strtotime(date(Ymd));
        $exptime = $nowtime + 3600 * 24 * 1; //提前天数

        $map['cid'] = $id;
        $firstDep = M('departure_time')->where('pid=' . $id . ' and departure_time>=' . $exptime . ' and is_del=0')->order('departure_time')->find();
        $info['startYear'] = date('Y', $firstDep['departure_time']);
        $info['startMonth'] = date('m', $firstDep['departure_time']);

        $info['service'] = json_decode($info['service']);
        $info['seo_desc_common'] = msubstr(strip_tags($info['info']), 45);
        $this->assign('info', $info);

        $this->display();
    }

    public function getDate()
    {
        $id = $_REQUEST['id'];
        $updays = M('goods')->where('id=' . $id)->getfield('sign_up');
        $month = $_REQUEST['month'];
        $mod = $_REQUEST['mod'];
        $year = $_REQUEST['year'];
        $starttime = strtotime($year . '-' . $month);
        if ($month == 12) {
            $endtime = strtotime(($year + 1) . '-1');
        } else {
            $endtime = strtotime($year . '-' . ($month + 1));
        }
        if (time() > $starttime) {
            $starttime = time() + ($updays - 1) * 86400;
            //$starttime = time();
        }
        $map['is_del'] = 0;
        $map['pid'] = $id;
        $map['departure_time'] = array('between', array($starttime, $endtime - 1));
        $list = M($mod)->where($map)->order('departure_time')->select();
        //echo M($mod)->getlastsql();
        foreach ($list as $key => $value) {
            $list2[date('j', $value['departure_time'])] = $value;
        }
        $json = $list ? json_encode($list2) : 0;
        //echo $json;
        $this->ajaxReturn($json, 'ok', 1);
    }

    //LIVE800
    public function chatad()
    {
        $adList = M('goods')->where('is_del=0 and is_show=1 and is_hot=1')->order('ordid desc')->limit(2)->select();
        foreach ($adList as $key => $value) {

            switch ($value['type_id']) {
                case 1:
                    $adList[$key]['catetype'] = 'zhoubian';
                    break;
                case 2:
                    $adList[$key]['catetype'] = 'guonei';
                    break;
                case 3:
                    $adList[$key]['catetype'] = 'chujing';
                    break;
                case 327:
                    $adList[$key]['catetype'] = 'suzhou';
                    break;
            }
        }

        $this->assign('adList', $adList);
        $this->display();
    }

    public function kefutest()
    {
        $id = 69;
        $mod = M('goods');
        $cateMod = M('goods_cate');
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        $this->assign('week', $weekarray);

        $info = $mod->where('id=' . $id)->find();
        $info['cateName'] = M('goods_cate')->where('id=' . $info['cate_id'])->getField('name');
        if (!$info) {
            $this->error('非法操作！', '/');
        }
        //自主团
        if (stripos($info['switch'], '1') or ($info['brand_id'] == 1)) {
            $info['is_zzt'] = 1;
        }

        //$info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
        $info['dep'] = $this->getDeparture($info['id'], 1);

        //最近行程
        $nowtime = strtotime(date(Ymd));
        $exptime = $nowtime + 3600 * 24 * 1;

        $map['cid'] = $id;
        $firstDep = M('departure_time')->where('pid=' . $id . ' and departure_time>=' . $exptime . ' and is_del=0')->order('departure_time')->find();
        $info['startYear'] = date('Y', $firstDep['departure_time']);
        $info['startMonth'] = date('m', $firstDep['departure_time']);

        $info['service'] = json_decode($info['service']);
        $info['seo_desc_common'] = msubstr(strip_tags($info['info']), 45);

        $floor = M('goods_cate')->where('id=' . $info['cate_id'])->getfield('floor');

        //面包屑
        $pidInfo = $cateMod->where('id=' . $info['cate_id'])->find();
        $ppid = $cateMod->where('id=' . $pidInfo['pid'])->getfield('pid');
        $key = $ppid ? $ppid : $pidInfo['pid'];
        switch ($key) {
            case 1:
                $this->assign('posi', "<a href='" . __ROOT__ . "/zhoubian/'>周边游</a><b>></b><a href='" . __ROOT__ . "/zhoubian/" . $pidInfo['pinyin'] . "/'>" . $pidInfo['name'] . "</a><b>></b>");
                $deptime = $nowtime + 3600 * 24 * 30;
                $depList = M('departure_time')->where('pid=' . $info['id'] . ' and departure_time>' . $nowtime . ' and departure_time<' . $deptime . ' and is_del=0')->order('departure_time')->select();
                $this->assign('depList', $depList);
                break;
            case 2:
                $this->assign('posi', "<a href='" . __ROOT__ . "/guonei/'>国内游</a><b>></b><a href='" . __ROOT__ . "/guonei/" . $pidInfo['pinyin'] . "/'>" . $pidInfo['name'] . "</a><b>></b>");
                $deptime = $nowtime + 3600 * 24 * 90;
                $depList = M('departure_time')->where('pid=' . $info['id'] . ' and departure_time>' . $nowtime . ' and departure_time<' . $deptime . ' and is_del=0')->order('departure_time')->select();
                $this->assign('depList', $depList);
                break;
            case 3:
                $this->assign('posi', "<a href='" . __ROOT__ . "/chujing/'>出境游</a><b>></b><a href='" . __ROOT__ . "/chujing/" . $pidInfo['pinyin'] . "/'>" . $pidInfo['name'] . "</a><b>></b>");
                $deptime = $nowtime + 3600 * 24 * 90;
                $depList = M('departure_time')->where('pid=' . $info['id'] . ' and departure_time>' . $nowtime . ' and departure_time<' . $deptime . ' and is_del=0')->order('departure_time')->select();
                $this->assign('depList', $depList);
                break;
            case 97:
                $this->assign('posi', "<a href='" . __ROOT__ . "/tuandui/'>团队游</a><b>></b><a href='" . __ROOT__ . "/tuandui/" . $pidInfo['pinyin'] . "/'>" . $pidInfo['name'] . "</a><b>></b>");
                break;
            case 326:
                $this->assign('posi', "<a href='" . __ROOT__ . "/suzhou/'>深度苏州</a><b>></b><a href='" . __ROOT__ . "/suzhou/" . $pidInfo['pinyin'] . "/'>" . $pidInfo['name'] . "</a><b>></b>");
                break;
        }

        //获取行程
        $trip = M('trip')->where('pid=' . $info['id'] . ' and is_del=0')->order('ordid')->select();
        foreach ($trip as $key => $value) {
            $trip[$key]['dinner'] = json_decode($value['dinner']);
            $scene = explode(',', $value['scene']);
            foreach ($scene as $skey => $svalue) {
                if ($svalue && $skey < 3) {
                    $res = M('scenic')->where('name="' . $svalue . '" and is_del=0')->find();
                    $trip[$key]['scenic'][$skey] = $res;
                }
            }

            //获取景点
            //$trip[$key]['scene'] = explode(',',$value['scene']);
        }
        $this->assign('trip', $trip);

        //相关线路 大分类下其他线路or同关键词线路or同分类
        //rList1为同小分类下其他线路，rList2为大分类下其他线路
        $rList1 = M('goods')->where('cate_id=' . $info['cate_id'] . ' and is_del=0 and is_show=1 and minprice<>0 and id !=' . $id)->order('ordid desc,add_time desc')->limit(4)->select();
        foreach ($rList1 as $key => $value) {
            $catepinyin = M('goods_cate')->where('id=' . $value['type_id'])->getfield('pinyin');
            $rList1[$key]['catepinyin'] = str_replace('you', '', $catepinyin);
        }
        //$rList2 = M('goods')->where('type_id='.$info['type_id'].' and is_del=0')->limit(4)->select();
        //$rList = array_merge($rList1,$rList2);
        //$rList = explode(array_unique(implode(',',$rList)));
        //var_dump($rList);
        $this->assign('relative', $rList1);

        //生成二维码
        $urlData = str_replace('/index.php', '', $_SERVER['PHP_SELF']); //替换掉index.php
        $curl = "http://" . $_SERVER['HTTP_HOST'] . $urlData;
        //$curl = 'http://www.33ly.com/chujing/xianlu-1087.html';
        $ewmurl = $this->erweima($curl, $info['sn'] . 'V1026');
        //echo $ewmurl;
        $this->assign('ewmurl', $ewmurl);

        if ($info['is_ds']) {
            $this->assign('info', $info);
            $this->display('detail_ds');
        } else {
            if ($info['is_zyx'] and ($info['type_id'] != 1 or $info['tpl_id'] == 1)) {
                //if(0){
                //交通信息
                $traffic = M('goods_flight')->where('pid=' . $info['id'] . ' and is_del=0')->order('ordid')->select();
                $this->assign('traffic', $traffic);
                //住宿信息
                $hotel = M('goods_hotel as gh')->join('33_hotel as h on h.hotel_id=gh.hotel_id')->where('gh.pid=' . $info['id'] . ' and gh.is_del=0')->order('gh.ordid')->select();
                foreach ($hotel as $key => $value) {
                    $hotel[$key]['img_list'] = M('hotel_gallery')->where('hotel_id=' . $value['hotel_id'])->select();
                }
                $this->assign('hotel', $hotel);
                $this->assign('info', $info);
                $this->display('ziyouxing');
            } else {
                $this->assign('info', $info);
                $this->display();
            }
        }

    }
}
