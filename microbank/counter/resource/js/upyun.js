/**
 * Created by 43070 on 2018/4/26.
 */

function getUPyunImgUrl(imagePath, version) {
    if (version)
        return upyun_url + imagePath + "!" + version;
    else
        return upyun_url + imagePath;
}