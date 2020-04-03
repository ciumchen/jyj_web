<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * topapi
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_image_upload implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '上传图片';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'upload_type'       => ['type'=>'string', 'valid'=>'required|in:binary,base64', 'desc'=>'上传图片类型，支持图片二进制流，base64'],
            'image'             => ['type'=>'binary|string',   'valid'=>'required', 'desc'=>'图片文件内容,不能为空'],
            'image_input_title' => ['type'=>'string', 'valid'=>'required', 'desc'=>'包括后缀名的图片标题,不能为空，如Bule.jpg, 图片上传后取图片文件的默认名'],
            'image_type'        => ['type'=>'string', 'valid'=>'required|in:item', 'desc'=>'图片类型，目前只支持上传item类型的图片'],
            'image_cat_id'      => ['type'=>'int',    'valid'=>'required', 'desc'=>'图片相册id,目前没有目录，直接输入0即可'],
        ];
    }

    /**
     * @return array 图片地址
     */
    public function handle($params)
    {
        $shopId = $params['oAuthInfo']['shop_id'];

        $imageService = $this->genImageService($shopId, $params['image_type'], 'shop', $image_cat_id);
        $imageObject = $this->genImageObject($params['upload_type'], $params['image_input_title'], $params['image']);

        $imageData = $imageService->store($imageObject, 'shop', $params['image_type']);
        $imageService->rebuild($imageData['ident'], $params['image_type']);

        $imageSrc['image_id'] = $imageData['url'];
        $imageSrc['url'] = base_storager::modifier($imageData['url']);
        $imageSrc['t_url'] = base_storager::modifier($imageData['url'],'T');

        return $imageSrc;
    }

    public function genImageService($shopId, $type, $from = 'shop', $image_cat_id = 0)
    {
        if($from != 'shop')
            throw LogicException(app::get('topmanageapi')->_('不支持的图片来源'));
        $objLibImage = kernel::single('image_data_image');
        $objLibImage->setUploadShopId($shopId);
        $objLibImage->setImageCatId($image_cat_id, $type);
        $objLibImage->setUploadImageAccount($from, $shopId);
        return $objLibImage;
    }

    public function genImageObject($upload_type, $image_input_title, $image)
    {

        if( $upload_type == 'binary' )
        {
            $image = $image;
        }
        else
        {
            if( preg_match('/^(data:\s*image\/(\w+);base64,)/', $image, $result) )
            {
                $image = base64_decode(str_replace($result[1], '', $image));
            }
            else
            {
                throw new \LogicException('上传失败');
            }
        }

        $tmpTarget = tempnam(TMP_DIR, 'image');

        file_put_contents($tmpTarget, $image);

        $imageParams = getimagesize($tmpTarget);
        $size = filesize($tmpTarget);

        return new UploadedFile($tmpTarget, $image_input_title, $imageParams['mime'], $size, 0, true);
    }

}


