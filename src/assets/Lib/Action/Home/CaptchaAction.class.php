<?php 
class CaptchaAction extends BaseAction {
    public function _empty() {
		import('ORG.Util.Image');
        Image::buildImageVerify(4, 1, 'gif', '50', '24', 'captcha');
	}
}