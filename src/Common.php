<?php
namespace GuzzleHttp;

// if ( session_id() == "" ) {
//     session_cache_limiter('private');
//     session_cache_expire(0);
//     session_start();
// }

use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use \GuzzleHttp\DB;
use \GuzzleHttp\TFP;

require_once __DIR__ . "/../../../common_files/STFSApiAccess.php";
// ini_set ("display_errors", 1);
// ini_set ("display_startup_errors", 1);
// error_reporting (E_ALL);



date_default_timezone_set("Asia/Tokyo");

$dirCommon = dirname(__FILE__) . "/../../../common_files/";
// require_once $dirCommon."/includes/debug/config.inc.php";
require_once $dirCommon . "webserver_flg.php";
require_once $dirCommon . "connect_db.php";
require_once $dirCommon . "STFSApiAccess.php";
require_once $dirCommon . "smtp_mail.php";
require_once $dirCommon . "security.php";
// require_once "classPack.php";
// require_once "classLoginAuth.php";
// require_once "classLoginAfter.php";
// require_once "classLoginAgri.php";
//　↓↓　＜2020/09/14＞　＜VinhDao＞　＜追加＞
require_once $dirCommon . 'security/index.php';
// require_once $dirCommon . 'security.php';
//　↑↑　＜2020/09/14＞　＜VinhDao＞　＜追加＞

//　↓↓　＜2020/12/10＞　＜Sang Cap＞　＜追加＞
require_once $dirCommon . 'products_version.php';
//　↑↑　＜2020/12/10＞　＜Sang Cap＞　＜追加＞

/**
 * @final
 */
class Common
{
    public static function Login_UserLogin($serial_no, $user_cd, $tel, $service_product, $version, &$message)
    {

        if ( empty($serial_no) || empty($user_cd) || empty($tel) ) {
            return false;
        }

        $serial_no = str_replace("-", "", htmlspecialchars($serial_no));
        $user_cd   = str_replace("-", "", htmlspecialchars($user_cd));
        $tel       = str_replace("-", "", htmlspecialchars($tel));
        $json  = '{
                "users":{
                    "data":[{"name":"user_cd","value":"' . $user_cd . '","operator":"="}]
                },
                "fields":"user_cd, tel1, tel2, tel3"
            }';
        $user  = TFP::getAPIDataAWS("users", $json, "GET");
        $count = (int) TFP::GetFirstByField($user, "count");
        if ( $count < 1 ) {

            return false;
        }

        $user = $user['users'][0];
        $json  = '{
                "users":{
                    "query":"user_cd = \'' . $user['user_cd'] . '\'"
                },
                "sral":{
                    "query":"sral_no = \'' . $serial_no . '\'"
                }
            }';
        $res   = TFP::getAPIDataAWS("users", $json, "GET");
        $count = (int) TFP::GetFirstByField($res, "count");
        if ( $count < 1 ) {
            return false;
        }
        $flagTel = false;
        $listTel = array( $user['tel1'], $user['tel2'], $user['tel3'] );

        foreach ( $listTel as $aTel ) {
            $temp = str_replace("-", "", $aTel);
            if ( $temp == $tel ) {
                $flagTel = true;
                break;
            }
        }
        if ( !$flagTel ) {
            return false;
        }

        // 契約情報取得
        $json  = '{"prod":{},"sral":{"data":[{"name":"sral_no","value":"' . $serial_no . '","operator":"="}]}}';
        $res   = TFP::getAPIDataAWS("prod", $json, "GET");
        $count = (int) TFP::GetFirstByField($res, "count");
        if ( $count < 1 ) {
            return false;
        }

        $res2   = Common::GetKyData($res["prod"][0]["prod_no"]);
        $count2 = (int) TFP::GetFirstByField($res2, "count");
        $kyHis  = $count2 > 0 ? $res2['ky'][0]['ky_his'][0] : null;

        // サービス利用可能かどうかのチェック
        if ( !CheckLoginAuth($kyHis, $serial_no, $service_product, $version, true, $message) ) {
            // 契約無し、他に所持するシリアルNoも無しの場合に「ログイン時にエラーが発生しました」が表示されるためコメント化
            // $message = '';
            // ユーザーコードをもとにシリアルNoをチェック
            //↓ Kentaro.Watanabe 2020/11/03
            // if (! CheckAllSerialNo($user_cd, $service_product, $version, $message)) {
            if ( !CheckAllSerialNo($user['user_cd'], $service_product, $version, $message) ) {
                //↑ Kentaro.Watanabe 2020/11/03
                return false;
            }
        }

        //↓ Kentaro.Watanabe 2020/11/03
        //↓↓　<2020/10/28> <YenNhi> <お客様コードが８と１２桁入れられる。>
        //SaveSession($serial_no, $user_cd);
        SaveSession($serial_no, $user['user_cd']);
        //↑↑　<2020/10/28> <YenNhi> <お客様コードが８と１２桁入れられる。>
        //↑Kentaro.Watanabe 2020/11/03

        // ログ出力
        OutputLoginLog('User', $serial_no, $service_product, $version);
        return true;
    }

    public static function GetKyData($prod_no, $kySyuKbPatarn = LOGIN_KY_SYU_KB_A)
    {
        $json = '{"ky":{"data":[{"name":"ky_syu_kb","value":"' . $kySyuKbPatarn . '","operator":"in"}],},"ky_prod":{"data":[{"name":"prod_no","value":"' . $prod_no . '","operator":"="}],},"ky_his":{"sort":"ky_his_ren desc"}}';
        $res  = TFP::getAPIDataAWS("ky", $json, "GET");

        // 契約情報があるとき場合のみ契約終了日(ky_e_ymd)を最新順にソート
        $count = (int) GetFirstByField($res, "count");
        if ( $count >= 1 )
            sort_ky_e_ymd($res);

        return $res;
    }

        // Save data into session after user login (not regist)
    public static function SaveSession($serial_no, $user_cd) {
        $LoginID = CreateLoginID($serial_no, $user_cd);
        UpdateSession($LoginID);
    // 2020/05/11 t.maruyama 修正 START サービスログインで使用するため引数を変更（$user_cdはもともと使用していない）
    //    $pData = GetTFPInfo($LoginID, $serial_no, $user_cd);
        $pData = GetTFPInfo($LoginID, $serial_no);
    // 2020/05/11 t.maruyama 修正 END
        SaveData($pData, LOGIN_PACK);
    }

    public static function GetTFPInfo($LoginID, $serial_no) {
        // 2020/05/11 t.maruyama 修正 END
            $pData = CheckSession($LoginID);
            $pData["PackName"] = GetPackName($serial_no);
            $json = '{
                "prod":{},
                "sral":{"query":"sral_no = \''.$serial_no.'\'"}
            }';
            $prod = TFP::getAPIDataAWS("prod", $json, "GET");
            $listErr = array();
            GetListByField($prod, $listErr, "err_msg");
            if (count($listErr) > 0 || $prod["total_count"] == 0) {
                Redirect("../", ERR_FIND_PROD);
            }
            $prod = $prod["prod"][0];
            $prod_no = $prod["prod_no"];
            if ($pData["PackName"] == "") {
                $json = '{"prod":{"query":"prod_no = \''.$prod_no.'\'"}, "sral":{}}';
                $prod = TFP::getAPIDataAWS("prod", $json, "GET");
                $prod = $prod["prod"][0];
                foreach ($prod["sral"] as $sral) {
                    Common::ExtractSralData($sral["sral_no"], $prod, $sral, $pData);
                }
            }
            else {
                Common::ExtractSralData($serial_no, $prod, $prod["sral"][0], $pData);
    
                $pairData = Common::GetPairData($serial_no);
                $pair_shin_cd = GetFirstByField($pairData, "shin_cd");
    
                /**
                 * 2022/01/31 haruka-suganuma
                 * ExtractSralData()内で$pDataへuser_cdの追加が無くエラーとなっていたため、TFから取得したuser_cdの利用に変更
                 **/  
                // $json = '{
                //     "prod":{"query":"user_cd = \''.$pData["user_cd"].'\'"},
                //     "sral":{"query":"shin_cd = \''.$pair_shin_cd.'\'"}
                // }';
                $json = '{
                    "prod":{"query":"user_cd = \''.$prod["user_cd"].'\'"},
                    "sral":{"query":"shin_cd = \''.$pair_shin_cd.'\'"}
                }';
                $prod = TFP::getAPIDataAWS("prod", $json, "GET");
                $listErr = array();
                GetListByField($prod, $listErr, "err_msg");
                if (count($listErr) > 0 || $prod["total_count"] == 0) {
                    Redirect("../", ERR_FIND_PROD);
                }
                $prod = $prod["prod"][0];
                $sral = $prod["sral"][0];
                Common::ExtractSralData($sral["sral_no"], $prod, $sral, $pData);
            }
            $pData["class"] = GetClass($serial_no, $pData);
            return $pData;
        }

        public static function ExtractSralData($serial_no, $prod, $sral, &$pData) {
            $Conn = DB::connectDB("ConnectTouroku");
            $pData["listSerial"][] = $serial_no;
            $curSerial = PREFIX.$serial_no;
            $shin_cd = $pData[$curSerial]["shin_cd"] = $sral["shin_cd"];
            $pData[$curSerial]["shin_nm"] = $sral["shin_nm"];
            $pData[$curSerial]["prod_no"] = $prod["prod_no"];
            $pshin_cd = $pData[$curSerial]["pshin_cd"] = $prod["pshin_cd"];
            $pData[$curSerial]["pshin_nm"] = $prod["pshin_nm"];
    
            $chose_cd = $shin_cd;
            if ($pshin_cd != "") {
                //　↓↓　＜2020/11/12＞　＜KhanhDinh＞　＜修正＞
                $json = '{
                        "shin":{
                            "data":[{"name":"shin_cd","value":"'.$pshin_cd.'","operator":"="}]
                        },
                        "pshin_struct":{},
                        "valid_fg" : "1"
                    }';
                //　↑↑　＜2020/11/12＞　＜KhanhDinh＞　＜修正＞
                $shin = TFP::getAPIDataAWS("shin", $json, "GET");
                $shin_su = GetFirstByField($shin, "shin_su");
                $pData[$curSerial]["shin_su"] = ($shin_su != "") ? (int)$shin_su : 0;
                $chose_cd = $pshin_cd;
            }
            $shin_bun_cd = GetSimpleField("shin", "GET", "shin_cd = '{$chose_cd}'", "shin_bun_cd");
            $pData[$curSerial]["shin_bun_cd"] = ($shin_bun_cd != "") ? $shin_bun_cd : 0;
    
            $sql = "SELECT CONCAT( IFNULL( Prd_dev_cd1, '' ), IFNULL( Prd_dev_cd2, '' ), IFNULL( Prd_dev_cd3 , '' ) ) AS version 
                FROM Product_Service_Master WHERE TFP_shin_cd = '".$sral["shin_cd"]."'";
            $result = mysqli_query($Conn, $sql);
            $res = mysqli_fetch_assoc($result);
            $pData[$curSerial]["version"] = $res["version"];
            mysqli_free_result($result);
            mysqli_close($Conn);
        }
    
        public static function GetPairData($serial_no) {
            $pack = FindPack($serial_no);
            if ($pack == false) {
                return false;
            }
    
            $pTemplate = $pack->GetTempPair($serial_no);
            if ($pTemplate === false) {
                return false;
            }
            $serial_pair = GetCheckDigit($pTemplate);
            return Common::GetGoods($serial_pair);
        }

        public static function GetGoods($serial_no) {
            $json = '{
                        "ver_range":{
                            "data":[{"name":"sral_no","value":"'.$serial_no.'","operator":"="}]
                        }
                    }';
            return TFP::getAPIDataAWS("ver_range", $json, "GET");
        }
}