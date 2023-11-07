<?php

namespace GuzzleHttp;

if ( session_id() == "" ) {
    session_cache_limiter('private');
    session_cache_expire(0);
    session_start();
}

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

$dir = dirname(__FILE__) . "/../reg/mailtemplate/";
define("PREFIX", "s");
// define("Mail_4b", $dir."シリアルNo. 登録範囲外エラー [\$serial_no].txt");
// define("Mail_21a", $dir."SMB-「ユーザー登録完了」のお知らせ.txt");
// define("Mail_21b", $dir."農業簿記-「ユーザー登録完了」のお知らせ.txt");
// define("Mail_21c", $dir."農業日誌-「ユーザー登録完了」のお知らせ.txt");
// define("Mail_30", $dir."「ユーザー登録＋バリューサポート」同時申込受付のお知らせ.txt");
// define("Mail_AddOn", $dir."「追加１ライセンス製品」ユーザー登録.txt");
define("Mail_4b", $dir . "Mail_4b.txt");
define("Mail_21a", $dir . "Mail_21a.txt");
define("Mail_21b", $dir . "Mail_21b.txt");
define("Mail_21c", $dir . "Mail_21c.txt");
define("Mail_30", $dir . "Mail_30.txt");
define("Mail_AddOn", $dir . "Mail_AddOn.txt");
define("Mail_MNRQ", $dir . "Mail_MNRQ.txt");
define("Mail_KCDRQ", $dir . "Mail_KCDRQ.txt");


// Use in saving session
define("LoginID", "Login-Session-ID");
define("LOGIN_PACK", "login_pack");
define("DOWNLOAD_LOGIN_PACK", "download_login_pack");
define("REGIST_PACK", "regist_pack");
define("LICENSE_PACK", "license_pack");
// 2022/10/28 <YenNhiTran> handle manual request
define("MNRQ_PACK", "manual_request_pack");
// 2022/12/09 <YenNhiTran> handle mkscd request
define("KCDRQ_PACK", "mkscd_request_pack");

// ↓↓　<2023/09/11> <KhanhDinh> <session for SmbPartnerMember>
define("PARTNER_MEMBER", "SmbPartnerMember");

define("DEF_USER_CD", "100000002102"); // Use in EntryConfirmation()
define("CHECK_EXPIRED", true); // Use in GetDataPack()
define("EXPIRED_LIMIT", "1 hour"); // Set time to delete session

define("URL_CHECKDIGIT", "http://192.168.3.213/admin_tool/checkdigit.asp");
define("URL_SORI_SUPPORT", "https://www.sorimachi.co.jp/usersupport/");
define("URL_SORI_SUPPORT_VALUE", "https://www.sorimachi.co.jp/usersupport/value/");
define("URL_SORI_SHOP", "https://www.sorimachi.co.jp/shop/");
define("URL_SORIZO", "https://www.sorizo.net/");

/***************************************************************************************************************
    define Error message
***************************************************************************************************************/

// EntryConfirmation(): [TFP_sral_hed] に、5ケタの数字が存在しない 
define("ERR_PSM0000", "ユーザー登録対象外の製品です。\n[エラーコード : PSM-0] ");

// EntryConfirmation(): -> [TFP_sral_hed] に存在する、[Srv_reg_flg] <> '0' のケースがある 
define("ERR_PSM0001", "ユーザー登録対象外の製品です。\n[エラーコード : PSM-1] ");

// EntryConfirmation(): シリアルNo.が発行されているか -> ver_range/ →エラーの場合
// 2020/11/12 mod Kentaro.Watanabe
define("ERR_TF0000", "ご入力いただいたシリアルNo.はすでに登録されている可能性があります。もしサービスがご利用になれない場合は、お問い合わせください。\n[エラーコード : TF-VR-1]");

// 2021/01/05 add Kentaro.Watanabe
define("ERR_TF0001", "ご入力いただいたシリアルNo.はすでに登録されている可能性があります。もしサービスがご利用になれない場合は、お問い合わせください。\n[エラーコード : TF-VR-2]");

// EntryConfirmation(): シリアルNo.が既に存在する。
define("ERR_TF5013", "ご入力いただいたシリアルNo.は、すでにユーザー登録されています。[エラーコード : TF-VR-1A]");

// entry_licenseX.php:「スタートパック」 「ファーマーズ・オフィス」製品に該当なしシリアルNo.
define("ERR_RG0000", "シリアルNo.の組み合わせが正しくありません。\n[エラーコード : TF-AP-1]");

/***************************************************************************************************************
    Come from wrong flow, use in: entry_insert.php, entry_confirm.php, regist_insert.php, vlsupport.php
***************************************************************************************************************/

// entry_insert.php, entry_confirm.php, regist_insert.php, vlsupport.phpで不正な操作が行っています
define("ERR_INVALID", "不正な操作が行われたため、処理を中断しました。前のページに戻って、再度操作を行なってください。\n[エラーコード : REG-0]");

// entry_licenseX.php: ２、３ライセンスを含んでいます場合に入力したシリアルNo.が重複しています
define("ERR_DUPLICATED", "入力したシリアルNo.が重複しています。\n[エラーコード : TF-AP-2]");

// LoginModule(): 入力した電話番号が正しくありません
define("ERR_TEL", "電話番号の入力内容をご確認ください。\n[エラーコード : REG-1]");

// license_input.php:条件を満たない入力したシリアルNo.（AddOnの資料に条件）
define("ERR_LICENSE_INPUT", "シリアルNo.が正しくありません。\n[エラーコード : LCS-1]");

// license_input.php: ライセンス登録を完了してから、メールを送信します
define("MSG_DONE_LICENSE", "追加ライセンスのご登録を受け付けました。");

// entry_done.php: ユーザーがvlsupportへ登録しました（sp_ky_kb = 1をチェック）
define("MSG_DONE_SUPPORT", "「ユーザー登録＋バリューサポート同時申込」が完了しました。");

// regist_insert.php: ユーザー情報を更新しました
define("MSG_DONE_UPDATE", "登録内容を更新しました。");

/***************************************************************************************************************
    define Error message (temporary): Need confirm from Japan
***************************************************************************************************************/

// EntryConfirmation(), entry_insert.php, vlsupport.php: メールを送信できません
define("ERR_MAIL", "メールの送信に失敗しました。\n[エラーコード : MAIL-0]");

// EntryConfirmation(): シリアルNo.値が空欄の場合
define("ERR_BLANK", "エラーが発生しました。入力画面から再度操作を行なってください。\n[エラーコード : CNF-0]");

// EntryConfirmation(): TFPからver_range情報を取得できません
define("ERR_VER_RANGE", "エラーが発生しました。入力画面に戻って、内容をご確認ください。\n[エラーコード : CNF-1]");

// CreateLoginID(): データベースにLogin-Session-IDを追加できません
define("ERR_REGIST_LOGINID", "ログイン時にエラーが発生しました。\n[エラーコード : LGN-1]");

// UpdateSession(): データベースへLogin-Session-IDを更新できません
define("ERR_UPDATE_LOGINID", "ログイン情報の更新でエラーが発生しました。\n[エラーコード : LGN-2]");

// entry_insert.php: TFPにprodを追加できません
define("ERR_CREATE_PROD", "製品情報の登録でエラーが発生しました。\n[エラーコード : TF-PRD-1]");

// GetTFPInfo(): Login-Session-IDからTFPに製品が見つかりません
define("ERR_FIND_PROD", "ログイン情報が正しくありません。\n[エラーコード : LGN-3]");

// CheckSession(): ログインのデータが不正です
define("ERR_LOGIN", "ログイン時にエラーが発生しました。\n[エラーコード : LGN-0]");

// ログインする必要です。
define("ERR_NOT_LOGIN", "ログインが必要です。\n[エラーコード : LGN-4]");

// entry_insert.php, regist_insert.php: user、prodへTFPのCheckコマンドを実施しているうちにエラーメッセージを表示します
define("ERR_CHECK", "データベースでエラーが発生しました。\n[エラーコード : TF-PRD-2]");

// entry_licenseX.php: ２、３ライセンスの場合に/reg/index.phpで入力したシリアルNo.と入力したシリアルNo.が一致しません
define("ERR_LICENSE", "シリアルNo.の組み合わせが正しくありません。入力内容をご確認ください。\n[エラーコード : TF-SP-1]");

// 必要ないし、削除できます
// LoginModule(): ログインのときに不足なデータ（user_cdやtelなど）が入力します
define("ERR_MISSING_DATA", "ログインが必要です。\n[エラーコード : LGN-5]");

// このセッションはタイムアウトしました
define("MSG_EXPIRED", "セッションは期限切れです。\n[エラーコード : LGN-6]");

// Check-Service-Login1
define("MSG_SERVICE_LOGIN1", "ご入力いただいたシリアルNoはサポート対象外の製品です。\nシリアルNoをご確認ください。
");

// Check-Service-Login2
define("MSG_SERVICE_LOGIN2", "ご入力いただいたシリアルNoは本サービスの対象製品ではありません。\nシリアルNoをご確認ください。");

// Check-Service-Login3
define("MSG_SERVICE_LOGIN3", "ご入力いただいたシリアルNoでは本サービスをご利用いただけません。\n契約状況とシリアルNoをご確認ください。");

// Check-Service-Login4
define("MSG_SERVICE_LOGIN4", "ご入力していただいたシリアルNoで、すでに他年度の「みんなの確定申告」がダウンロードされています。");

// Check-Download-Login1
define("MSG_DOWNLOAD_LOGIN1", "ご入力いただいたシリアルNoは本製品のシリアルNoではありません。\n入力内容をご確認ください。");


define("No_REG", "ユーザー登録が未完了です。");

define("No_SERIAL", "シリアルNoを入力してください");

// regist_certify.php: [シリアルNo][電話番号][お客様コード]の値が一致しない
define("ERR_REGIST_CERTIFY_LOGIN_1", "ご入力内容とユーザー登録情報に相違があります。<br>「製品シリアルナンバー」「電話番号」「お客様コード」の入力内容をご確認の上再度ご入力ください。 [エラーコード : LGN-RC-1]");

//option code: select from [service_db].[reg_option_cd] has 0 record
define("ERR_OCD_001", "オプションコードの入力内容をご確認ください。[ERR-OCD-001]");

//option code: select from [service_db].[reg_option_cd] has [code_s_ymd] > [now datetime]
define("ERR_OCD_002", "入力されたオプションコードは使用期間前のため、ご利用になれません。[ERR-OCD-002]");

//option code: select from [service_db].[reg_option_cd] has [code_e_ymd] < [now datetime]
define("ERR_OCD_003", "入力されたオプションコードの使用期限を過ぎているため、ご利用になれません。[ERR-OCD-003]");

//option code: shin_cd is not in [target_shin_cd] - select from [service_db].[reg_option_code_master]
define("ERR_OCD_011", "入力されたオプションコードは、対象外のため使用できません。[ERR-OCD-011]");

//option code: restrict pshin_cd by [exclude_pshin_flg] - select from [service_db].[reg_option_code_master]
define("ERR_OCD_012", "入力されたオプションコードは、ライセンスパックでは使用できません。[ERR-OCD-012]");

//header_hdr.php: sralno-何も入力されていない
define("ERR_SRNW_001", "シリアルNo.が入力されていません。[err-srnw-001]");

//header_hdr.php: sralno-半角数字と半角ハイフン以外の文字が入力された
define("ERR_SRNW_002", "シリアルNo.の入力内容をご確認ください。[err-srnw-002]");

//1文字目のtext が、「2」ではない
define("ERR_SRNW_101", "入力されたシリアルNo.は対象外の製品です。[err-srnw-101]");

// それ以外のshin_cdの場合は、errorを表示してください
define("ERR_SRNW_201", "最新製品のシリアルNo.が取得できません。[err-srnw-201]");

// mnrq: sral_no, tel が、半角数字のみならOK, 半角数字以外のtextがある -> NG 
define("ERR_MNRQ_101", "シリアルNo.と電話番号の入力内容をご確認ください。[ERR-MNRQ-101]");

//mnrq: check [checkUsersValue_sn_tl] return false
define("ERR_MNRQ_102", "シリアルNo.と電話番号の入力内容をご確認ください。[ERR-MNRQ-102]");

//mnrq: check [checkUsersValue_sn_tl] return false
define("ERR_MNRQ_111", "パッケージ内にマニュアル冊子が同梱されているため、送付のお申込はできません。[ERR-MNRQ-111]");

//mnrq: check pshin_cd not empty
define("ERR_MNRQ_112", "パッケージ内にマニュアル冊子が同梱されているため、送付のお申込はできません。[ERR-MNRQ-112]");

//mnrq: Srv_mnrq_flg = '1'
define("ERR_MNRQ_113", "マニュアル送付のお申込みを受け付けていない製品です．[ERR-MNRQ-113]");

//mnrq: prod_syu_kb <> '0' NG
define("ERR_MNRQ_114", "マニュアル送付のお申込みを受け付けていない製品です（製品種別）。[ERR-MNRQ-114]");

//mnrq: prod_bun_cd <> '0' || prod_bun_cd <> '25' NG
define("ERR_MNRQ_115", "マニュアル送付のお申込みを受け付けていない製品です（製品分類）。[ERR-MNRQ-115]");

//mnrq: GET TFP [valid_st_ymd] < (now date) < [valid_ed_ymd] ではない場合（期間外の場合）、NG
define("ERR_MNRQ_121", "商品が送付期間外です。[ERR-MNRQ-121]");

//mnrq: TFP [valid_st_ymd](shin_parts) < (now date) < [valid_ed_ymd](shin_parts) ではない場合（期間外の場合）、NG
define("ERR_MNRQ_122", "マニュアルセットが送付期間外です。[ERR-MNRQ-122]");

//mnrq: GET [TFP] pshin_cd/shin_cd、count = 0 の場合、NG
define("ERR_MNRQ_123", "マニュアル送付申込の対象外製品、またはバージョンです。[ERR-MNRQ-123]");

//mnrq: Check : [TF] user_cd, prod_no -> すでに申込されていないか、count > 0 の場合、NG
define("ERR_MNRQ_131", "すでにマニュアル送付を受け付けているため、お申込みできません。[ERR-MNRQ-131]");

//mnrq: Check : [TF] user_cd, prod_no -> すでに申込されていないか、count > 0 の場合、NG
define("ERR_MNRQ_301", "マニュアルセットの情報が取得できませんでした。[ERR-MNRQ-301]");

//mnrq: Check : [TF] user_cd, prod_no -> すでに申込されていないか、を確認
define("ERR_MNRQ_302", "すでにマニュアル送付を受け付けが完了しています。[ERR-MNRQ-302]");

//mnrq: Check : MKSCD Request の session value が保存されていない場合はNG
define("ERR_MNRQ_303", "ログイン情報の保存期限が過ぎました。お手数ですが、再度ログイン画面からご入力ください。[ERR-MNRQ-303]");

//mnrq: [TF] delv_his/ api POST to send mail , NG
define("ERR_MNRQ_311", "データの登録に失敗しました。[ERR-MNRQ-311]");

//mnrq: send mail , NG
define("ERR_MNRQ_321", "メールの送信に失敗しました。[ERR-MNRQ-321]");

//kcdrq: sral_no, tel が、半角数字のみならOK, 半角数字以外のtextがある -> NG 
define("ERR_KCDRQ_101", "シリアルNo.と電話番号の入力内容をご確認ください。[ERR-KCDRQ-101]");

//kcdrq: function  checkUsersValue_sn_tl FALSE -> NG
define("ERR_KCDRQ_102", "シリアルNo.と電話番号の入力内容をご確認ください。[ERR-KCDRQ-102]");

//kcdrq: [shin_cd] not in list -> NG
define("ERR_KCDRQ_112", "本サービスの対象製品は「会計王22」「会計王22PRO」「みんなの青色申告22」のみとなります。[ERR-KCDRQ-112]");

//kcdrq: GET : [TF] sral_no -> user_cd/prod_no/shin_cd を取得, count = 0 -> NG
define("ERR_KCDRQ_111", "本サービスのお申込みには、ユーザー登録及びバリューサポートへのご加入が必要です。[ERR-KCDRQ-111]");

//kcdrq: GET : [TF] prod_no -> ky, ky_hisをすべてGET, count = 0 -> NG
define("ERR_KCDRQ_121", "本サービスのお申込みには、バリューサポートへのご加入が必要です。[ERR-KCDRQ-121]");

//kcdrq: ky_shu_kb = 6 AND ky_his_syu_kb = 1 or 2 or 4 or 5
define("ERR_KCDRQ_122", "SAAG会員様は、本ページからのお申し込みはできません。お手数ですが、担当営業もしくはソリマチパートナー事務局までお問い合わせください。[ERR-KCDRQ-122]");

//kcdrq: 「ky_shu_kb = 1」AND「ky_his_syu_kb = 1 or 2 or 4 or 5」の record が 0件-> NG
define("ERR_KCDRQ_123", "本サービスのお申込みには、バリューサポートへのご加入が必要です。[ERR-KCDRQ-123]");

//kcdrq: ky_e_ymd < 2022/11/1 -> NG 
define("ERR_KCDRQ_124", "バリューサポートの契約期間が終了しているため、お申込みできません。[ERR-KCDRQ-124]");

//kcdrq: Check : [TF] user_cd, prod_no -> すでに申込されていないか、を確認, count > 0 -> NG
define("ERR_KCDRQ_131", "すでに「みんなの確定申告CD-ROM」送付を受け付けているため、お申込みできません。[ERR-KCDRQ-131]");

//kcdrq: GET : [TF] parts_cd -> hmoto_kb を取得, count => 0 or Error の場合、-> NG
define("ERR_KCD_301", "部材マスタの情報が取得できませんでした。[ERR-KCD-301]");

//kcdrq: check step 3.1.3a
define("ERR_KCDRQ_302", "すでに「みんなの確定申告CD-ROM」送付の受け付けが完了しています。[ERR-KCDRQ-302]");

//kcdrq: Check : MKSCD Request の session value が保存されていない場合はNG
define("ERR_KCDRQ_303", "ログイン情報の保存期限が過ぎました。お手数ですが、再度ログイン画面からご入力ください。[ERR-KCDRQ-303]");

//kcdrq: [TF] delv_his/ api POST, if have error -> NG
define("ERR_KCDRQ_311", "データの登録に失敗しました。[ERR-KCDRQ-311]");

//kcdrq: send mail failed
define("ERR_KCDRQ_321", "メールの送信に失敗しました。[ERR-MNRQ-321]");


//kcdrq: 毎年、変更します
define("VALUE_MKSCD_PARTS_CD", "SNM230003300");
define("VALUE_START_SHIPPING_DATE", "2023-02-01");


//***********************************************************************/
//* 全製品共通指定 各種ダウンロードサーバー
//***********************************************************************/
global $MEMBER_SP_DOWNLOAD_SERVER;
$MEMBER_SP_DOWNLOAD_SERVER = "http://www.sorimachi.on.arena.ne.jp/sp/";
global $MEMBER_SP_DOWNLOAD_SERVER_AWS;
$MEMBER_SP_DOWNLOAD_SERVER_AWS = "https://sorimachi-download.s3-ap-northeast-1.amazonaws.com/sp/";
global $MEMBER_SP_DOWNLOAD_SERVER_DL1;
$MEMBER_SP_DOWNLOAD_SERVER_DL1 = "https://sorimachi-download.s3-ap-northeast-1.amazonaws.com/sp/";
global $MEMBER_PRG_DOWNLOAD_SERVER;
$MEMBER_PRG_DOWNLOAD_SERVER = "http://www.sorimachi.on.arena.ne.jp/program/";
global $MEMBER_PRG_DOWNLOAD_SERVER_AWS;
$MEMBER_PRG_DOWNLOAD_SERVER_AWS = "https://sorimachi-download.s3-ap-northeast-1.amazonaws.com/prg/";
global $MEMBER_PRG_DOWNLOAD_SERVER_DL1;
$MEMBER_PRG_DOWNLOAD_SERVER_DL1 = "http://dl1.sorimachi.co.jp/prg/";
global $MEMBER_PRG_DOWNLOAD_SERVER_RES;
$MEMBER_PRG_DOWNLOAD_SERVER_RES = "https://res.sorimachi.co.jp/files_prg/";
global $MEMBER_AdobeReaderDL_URL;
$MEMBER_AdobeReaderDL_URL = "http://www.adobe.co.jp/products/acrobat/readstep2.html";

// global $listSPack;
// if (!is_array($listSPack)) {
//     $listSPack = array();
//     $listSPack[] = new ProdPack("スタートパック", array("1021-12x5-xxxxx", "1015-12x0-xxxxx-xxx"), true);
//     $listSPack[] = new ProdPack("スタートパック", array("1021-12x4-xxxxx", "1014-12x0-xxxxx-xxx"), true);
//     $listSPack[] = new ProdPack("ファーマーズ・オフィス", array("1021-12x3-xxxxx", "1013-12x0-xxxxx-xxx"), true);
//     $listSPack[] = new ProdPack("ファーマーズ・オフィス", array("1021-12x2-xxxxx", "1012-12x2-xxxxx"), true);
//     $listSPack[] = new ProdPack("ファーマーズ・オフィス", array("1021-12x1-xxxxx", "1011-12x1-xxxxx"), true);
// }

/**
 * @final
 */
class Common1
{
    public static function Login_UserLogin($serial_no, $user_cd, $tel, $service_product, $version, &$message){
        return 0;
    }
}