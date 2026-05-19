<?php
class ExcelHandleAction extends BaseAction{

    public function __construct() {
        parent::__construct();
        Vendor("PHPExcel.PHPExcel"); //引入phpexcel类(注意你自己的路径)  
        Vendor("PHPExcel.PHPExcel.IOFactory");
    }

    /**
     * 导出数据到表格文件
     * @param $expTitle     string File name
     * @param $expCellName  array  Column name
     * @param $expTableData array  Table data
     */
    public function exportExcel($expTitle, $expCellName, $expTableData) {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
        $fileName = $xlsTitle . date('_YmdHis'); //or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

        $objPHPExcel = new PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1'); //合并单元格
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . '  Export time:' . date('Y-m-d H:i:s'));
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 2), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }

	/**
     * 电商线路按查询条件导出到EXCEL表
     */
	function daoyouToExcel(){
		$xlsName = 'daoyou_info'; //表名
		$xlsModel = D($xlsName);
		
        /*
        * 定制输出字段的数组*/
        $data = array(
            array('cname', '姓名'),
            array('cphone', '电话'),
        ); 
		
		//构建数据
		$xlsData = $xlsModel->select();
		
		//C('OUTPUT_ENCODE',FALSE);必须把页面压缩关掉
        $this->exportExcel($xlsName, $data, $xlsData);
		//$this->ajaxReturn(1,1,1);
	}
	
	/**
     * 电商线路按查询条件导出到EXCEL表
     */
	function lineToExcel(){
		$xlsName = 'goods'; //表名
		$xlsModel = D($xlsName);
		
        /*
        * 定制输出字段的数组*/
        $data = array(
            array('lineIndex', '序号'),
            array('lineCateName', '目的地'),
            array('lineSN', '后台编号'),
		    array('lineName', '线路名'),
		    array('lineLastDepture', '团期截止'),
		    array('lineDeptureCount', '开班数'),
			array('lineMinPrice', '团费'),
        ); 
		
		//构建数据
		$where = 'is_del=0 and is_show=1 and type_id<>97';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND (name LIKE '%".$_REQUEST['keyword']."%' or subname LIKE '%".$_REQUEST['keyword']."%')";
		}
		if (isset($_REQUEST['sn']) && trim($_REQUEST['sn'])) {
			$where .= " AND (sn LIKE '%".$_REQUEST['sn']."%')";
		}
		$cateArr['f1'] = $_REQUEST['f1']?$_REQUEST['f1']:0;
		$cateArr['f2'] = $_REQUEST['f2']?$_REQUEST['f2']:0;
		$cateArr['f3'] = $_REQUEST['f3']?$_REQUEST['f3']:0;
		$arr = M('GoodsCate')->where('is_del=0')->select();
		if($cateArr['f3']){
			$where .= " AND cate_id=".$cateArr['f3'];
		}elseif($cateArr['f2']){
			$cate_arr = getEndChild($arr,$cateArr['f2']);
			if(empty($cate_arr)){
				$where .= " AND cate_id=".$cateArr['f2'];
			}else{
				$where .= " AND cate_id in (".implode(",",$cate_arr).")";
			}	
		}elseif($cateArr['f1']){
			$cate_arr = getEndChild($arr,$cateArr['f1']);
			$where .= " AND cate_id in (".implode(",",$cate_arr).")";
		}
		$xlsData = $xlsModel->where($where)->order('cate_id desc,ordid desc')->select();
		foreach($xlsData as $key=>$value){
			$xlsData[$key]['lineIndex'] = $key+1;
			$lineCateName = M('goodsCate')->where('id="'.$value['cate_id'].'"')->getfield('name');
			$xlsData[$key]['lineCateName'] = $lineCateName;
			$xlsData[$key]['lineSN'] = $value['sn'];
			$xlsData[$key]['lineName'] = '<'.$value['name'].'>'.$value['subname'];
			$lineLastDepture = M('departure_time')->where('pid="'.$value['id'].'"')->order('departure_time desc')->limit(1)->select();
			$xlsData[$key]['lineLastDepture'] = date('Y-m-d',$lineLastDepture[0]['departure_time']);
			$lineDeptureCount = M('departure_time')->where('pid="'.$value['id'].'"')->count();
			$xlsData[$key]['lineDeptureCount'] = $lineDeptureCount;
			$xlsData[$key]['lineMinPrice'] = $value['minprice'];
		}
		
		//C('OUTPUT_ENCODE',FALSE);必须把页面压缩关掉
        $this->exportExcel($xlsName, $data, $xlsData);
		//$this->ajaxReturn(1,1,1);
	}

    /**
     * 导出数据到表格文件(订单)
     * @param string $table 表名
     */
    function expData($table) {//导出Excel
        if ($table == '') {
            $table = $_REQUEST['table'];
        }
        $xlsName = $table;
        $xlsModel = D($table);
        $fields = $xlsModel->getDbFields();

        foreach ($fields as $v) {
            $str.="'" . $v . "',";
        }
        $str = substr($str, 0, strlen($str) - 1);

        $table = C('DB_PREFIX') . $table;
        $dbname = C('DB_NAME');
        //查询表字段的备注信息
        $xlsCell = $xlsModel->query("SELECT  column_name, column_comment from  Information_schema.columns where column_name in ({$str}) and table_Name='{$table}' and table_schema='{$dbname}'"); //查询字段备注信息,这么做要确定字段已填备注

        foreach ($xlsCell as $k => $v) {
            if (isset($fields[$k])) {
                $data[] = array($v['column_name'], $v['column_comment']);
            }
        }
        /*
         * 定制输出字段的数组*/
          $data = array(
          array('add_day', '下单日期'),
          array('ordstart', '出发日期'),
          array('ordsn', '订单编号'),
		  array('cname', '姓名'),
		  array('cphone', '电话'),
		  array('add_time', '下单时间'),
		  array('ordfrom', '订单来源'),
		  array('ordacc', '订单入口'),
		  array('ordaccc', '订单入口分类'),
		  array('ordname', '线路名称'),
		  array('', '线路分类'),
		  array('ordprice', '订单金额'),
		  array('', '利润'),
		  array('ordstatus', '订单状态'),
		  array('clsrz', '取消原因'),
		  array('adult_num', '成人数'),
		  array('child_num', '儿童数'),
          ); 
		
		$nowTime = strtotime(date('Y-m-d',(time()-(3600*24*150))));
		
		
        $xlsData = $xlsModel->where('add_time>'.$nowTime)->order('add_time')->select();
		//处理数据
		foreach($xlsData as $key=>$value){
			
			$xlsData[$key]['add_day'] = date('Y年n月d日',$value['add_time']);
			$xlsData[$key]['add_time'] = date('Y/n/d G:i',$value['add_time']);
			if($value['ordfrom']){
				$xlsData[$key]['ordfrom'] = '手机';
			}else{
				$xlsData[$key]['ordfrom'] = '网站';
			}
			$xlsData[$key]['ordstart'] = date('Y年n月d日',$value['ordstart']);
			switch($value['ordacc']){
				case '33ly.com':
					$xlsData[$key]['ordaccc'] = '直接访问';
					break;
				case 'm.33ly.com':
					$xlsData[$key]['ordaccc'] = '直接访问';
					break;
				case 'mm.33ly.com':
					$xlsData[$key]['ordaccc'] = '直接访问';
					break;
				case 'www.33ly.com':
					$xlsData[$key]['ordaccc'] = '直接访问';
					break;
				case 'changshu.33ly.com':
					$xlsData[$key]['ordaccc'] = '直接访问';
					break;
				case 'kunshan.33ly.com':
					$xlsData[$key]['ordaccc'] = '直接访问';
					break;
				case 'wx':
					$xlsData[$key]['ordaccc'] = '微信公众号';
					break;
				case 'qian':
					$xlsData[$key]['ordaccc'] = '微信公众号';
					break;
				case 'singlemessage':
					$xlsData[$key]['ordaccc'] = '微信分享';
					break;
				case 'groupmessage':
					$xlsData[$key]['ordaccc'] = '微信分享';
					break;
				case 'timeline':
					$xlsData[$key]['ordaccc'] = '微信分享';
					break;
				case 'bzclk.baidu.com':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;				
				case 'baiduWAP':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduPC':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduPC-shangye':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-index':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-taiwan':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-shanghua':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-qiandaohu':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-hainan':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduPC-index':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-riben':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduPinPai':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'baiduWAP-riben':
					$xlsData[$key]['ordaccc'] = '百度竞价';
					break;
				case 'www.baidu.com':
					$xlsData[$key]['ordaccc'] = 'SEO';
					break;
				case 'm.baidu.com':
					$xlsData[$key]['ordaccc'] = 'SEO';
					break;
				case 'm.sp.sm.cn':
					$xlsData[$key]['ordaccc'] = 'SEO';
					break;
				default:
					$xlsData[$key]['ordaccc'] = 'SEO';
					break;
			}
			switch($value['ordstatus']){
				case '0':
					$xlsData[$key]['ordstatus'] = '等待确认';
					break;
				case '1':
					$xlsData[$key]['ordstatus'] = '等待支付';
					break;
				case '2':
					$xlsData[$key]['ordstatus'] = '待发出团通知书';
					break;
				case '3':
					$xlsData[$key]['ordstatus'] = '交易完成';
					break;
				case '4':
					$xlsData[$key]['ordstatus'] = '转门店跟进';
					break;
				case '5':
					$xlsData[$key]['ordstatus'] = '沟通进行中';
					break;
				case '-1':
					$xlsData[$key]['ordstatus'] = '用户取消';
					break;
				case '-2':
					$xlsData[$key]['ordstatus'] = '后台取消';
					break;
			}
			switch($value['clsrz']){
				case '1':
					$xlsData[$key]['clsrz'] = '人数不满，不成团';
					break;
				case '2':
					$xlsData[$key]['clsrz'] = '产品未及时更新';
					break;
				case '3':
					$xlsData[$key]['clsrz'] = '报满，无位置';
					break;
				case '4':
					$xlsData[$key]['clsrz'] = '无效订单';
					break;
				case '5':
					$xlsData[$key]['clsrz'] = '门店客户';
					break;
				case '6':
					$xlsData[$key]['clsrz'] = '其他原因';
					break;
				case '7':
					$xlsData[$key]['clsrz'] = '客人原因取消订单';
					break;
				default:
					$xlsData[$key]['clsrz'] = '';
					break;
			}
			
		}
        //C('OUTPUT_ENCODE',FALSE);必须把页面压缩关掉
        $this->exportExcel($xlsName, $data, $xlsData);
    }	
	
    /**
     * 读取文件生成数组
     * @param string $filename 文件路径
     * @param string $encode  编码
     * @param string $file_type 文件类型
     * @param string $table 表名
     * @return array 读取文件生成的数组
     */
    public function read($filename, $encode, $file_type, $table) {
        if (strtolower($file_type) == 'xls') {//判断excel表类型为2003还是2007  
            Vendor("Excel.PHPExcel.Reader.Excel5");
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        } elseif (strtolower($file_type) == 'xlsx') {
            Vendor("Excel.PHPExcel.Reader.Excel2007");
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);

        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();

        //获取表字段,这么写，表格文件列顺序必须与数据库表的字段顺序相同
        $m = D($table);
        $field = $m->getDbFields();

        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {

                $excelData[$row][(string) $objWorksheet->getCellByColumnAndRow($col, 1)->getValue()] = (string) $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();//字段名存于excel表中
                //$excelData[$row][] = (string) $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                //从表中获取字段
                //$excelData[$row][$field[$col + 1]] = (string) $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }	
	
    public function add() {

        $file_types = explode(".", $_FILES ['import'] ['name']);
        $file_type = $file_types [count($file_types) - 1];
        /* 判别是不是.xls文件，判别是不是excel文件 */

        if (strtolower($file_type) != "xlsx" && strtolower($file_type) != "xls") {
            $this->error('不是Excel文件，重新上传');
        }
		mkdir('./Uploads/table/');
        $fileinfo = $this->uploadFile('./Uploads/table/', 2); //文件返回的是文件的路径

        $res = $this->read($fileinfo[0]['savepath'].$fileinfo[0]['savename'], "UTF-8", $file_type, $_POST['table']); //传参,判断office2007还是office2003  
		sort($res); //用addall数组索引必须从0开始
		
        $kucun = M($_REQUEST['table']); //M方法
		//获取wid
		$kucun->where('wid='.$_REQUEST['wid'])->delete();
		foreach($res as $key=>$value){
			$res[$key]['wid'] = $_REQUEST['wid'];
			$kucun->add($res[$key]);
		}
		$this->success('导入成功',U('Wxwin/index'));
        //$kucun->create($res);
        //$result = $kucun->addAll($res);
		

        // if (!$result) {
            // $this->error('导入数据库失败');
            // exit();
        // } else {
            // $this->success('导入成功');
        // }
    }	

}

