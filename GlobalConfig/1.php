<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (empty($_SERVER['HTTP_REFERER']) || empty($_GET['vui']))
{
	die('Method not defined for CloudFront instance. Contact: admin@redezaptank.com.br');
}
$CryptServer = strstr($_GET['vui'], '?rnd=', true);
echo '<root>
<config>
<XML_VERSION value="995"/> <!-- CACHE UPDATE EM ASHX OU XML ACRESCENTE UM NÚMERO -->
<IMG_VERSION value="87"/> <!-- CACHE UPDATE EM IMAGENS ACRESCENTE UM NÚMERO -->
<SWF_VERSION value="87"/> <!-- CACHE UPDATE EM SWF ACRESCENTE UM NÚMERO -->
<BACKUP_FLASHSITE value="//cdn.redezaptank.com.br/flash11700v12/"/>
<USE_MD5 value="false"/>
<FLASHSITE value="//cdn.redezaptank.com.br/flash11700v12/"/>
<SITE value="https://cdn.redezaptank.com.br/resourcev127/"/>
<res_SITE value="//cdn.redezaptank.com.br/flash11700v12/"/>
<FIRSTPAGE value="https://redezaptank.com.br/play?sid='.$CryptServer.'"/>
<REGISTER value="https://redezaptank.com.br/play?sid='.$CryptServer.'"/>
<REQUEST_PATH value="https://quests1.redezaptank.com.br/"/>
<LOGIN_PATH value="https://redezaptank.com.br/play?sid='.$CryptServer.'"/>
<FILL_PATH value="https://redezaptank.com.br/viplist?page=vipitemlist&server='.$CryptServer.'"/>
<POLICY_FILES>
<file value="https://redezaptank.com.br/crossdomain.xml"/>
</POLICY_FILES>
<ALLOW_MULTI value="true"/>
<FIGHTLIB value="true"/>
<TRAINER_PATH value="tutorial.swf"/>
<MUSIC_LIST value="1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1023,1024,1025,1026,1027,1028,1029,1030,1031,1032,1034,1035,1036,1037,1038,1039,1040,1059,1060,1061,1062,1063,1065,1067,1068,1069,1077"/>
<LANGUAGE value="portugal"/>
<PARTER_ID value="1001361"/>
<STATISTIC value="true"/>
<SUCIDE_TIME value="120"/>
<ISTOPDERIICT value="true"/>
<COUNT_PATH value="#"/>
<PHP isShow="false" link="false" site="#" infoPath="a.xml"/>
<OFFICIAL_SITE value="https://redezaptank.com.br/"/>
<GAME_FORUM value="https://redezaptank.com.br/"/>
<COMMUNITY_FRIEND_PATH isUser="false" value="https://redezaptank.com.br/"/>
<COMMUNITY_INVITE_PATH value="https://redezaptank.com.br/"/>
<COMMUNITY_FRIEND_LIST_PATH value="" isexist="false"/>
<COMMUNITY_FRIEND_INVITED_SWITCH value="false" invitedOnline="false"/>
<COMMUNITY_MICROBLOG value="false"/>
<EXTERNAL_INTERFACE enable="true" path="https://redezaptank.com.br/" key="cr64rAmUPratutUp" server="t1"/>
<USERS_IMPORT_ACTIVITIES path="https://redezaptank.com.br/" enable="false"/>
<ALLOW_POPUP_FAVORITE value="true"/>
<FILL_JS_COMMAND value="showPayments" enable="false"/>
<SHIELD_NOTICE value="false"/>
<STHRENTH_MAX value="12"/>
<FEEDBACK enable="false"/>
<USER_GUILD_ENABLE value="true"/>
<MINLEVELDUPLICATE value="10"/>
<TEACHER_PUPIL_FB enable="true"/>
<GUILD_SKILL enable="true"/>
<LEAGUE enable="true"/>
<HOTSPRING value="false"/>
<CONSORTIA_NAME_CHANGECOLOR enable="true" color="0xFF0000" value="12"/>
<DAILY enable="true"/>
<CLIENT_DOWNLOAD value="https://redezaptank.com.br/launcher"/>
<IS_SEND_FLASHINFO value="true"/>
<TEXPBTN value="true"/>
<PLACARD_TASKBTN value="true"/>
<BADGEBTN value="true"/>
<DOWNLOAD value="true"/>
<LUCKY_NUMBER enable="false"/>
<LOTTERY enable="false"/>
<MODULE>
<CIVIL enable="true"/>
<CHURCH enable="true"/>
</MODULE>
<CHAT_FACE>
<DISABLED_LIST list="38"/>
</CHAT_FACE>
<GAME_FRAME_CONFIG>
<FRAME_TIME_OVER_TAG value="67"/>
<FRAME_OVER_COUNT_TAG value="25"/>
</GAME_FRAME_CONFIG>
<SHORTCUT enable="false"/>
<GAME_BOXPIC value="1"/>
<BUFF enable="true"/>
<LITTLEGAMEMINLV value="10"/>
<SHOW_BACKGROUND value="true"/>
<TRAINER_STANDALONE value="true"/>
<OVERSEAS>
<OVERSEAS_COMMUNITY_TYPE value="1" callPath="" callJS=""/>
</OVERSEAS>
<DUNGEON_OPENLIST value="1,2,3,4,5,6,7,8,9,10,11,12,70001,12016,15001,16001" advancedEnable="true" epicLevelEnable="true" footballEnable="true"/>
<SHOPITEM_SUIT_TOSHOW enable="true"/>
<SUIT enable="true"/>
<KINGBLESS enable="true"/>
<QUEST_TRUSTEESHIP enable="false"/>
<WARRIORS_FAM enable="true"/>
<GEMSTONE enable="true"/>
<GOTO337 value="#"/>
<ONEKEYDONE enable="true"/>
<ENCOUNTER enable="false"/>
<TREASURE enable="false" time="5"/>
<ENERGY_ENABLE enable="fales"/>
<EXALTBTN enable="true"/>
<GODSYAH enable="true"/>
<PK_BTN enable="true"/>
<FIGHT_TIME count="2"/>
<PETS_EAT enable="true"/>
<MAGICHOUSE enable="true"/>
<GIRLHEAD enable="false" value=""/>
<GIRDATTEST enable="false"/>
<MAGICBOXBTN enable="true"/>
<BAGINFOGODTEMPLE enable="false"/>
<RESOURCE_SITE value="//cdn.redezaptank.com.br/flash11700v12/"/>
<CROSSBUGGlLEBTN enable="true"/>
<CROSSBUGGLE enable="true"/>
</config>
<update>
<version from="11601" to="11602">
<file value="*"/>
</version>
</update>
</root>
';
?>