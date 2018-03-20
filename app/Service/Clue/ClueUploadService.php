<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 下午4:05
 */

namespace App\Service\Clue;


use App\Repository\Clue\FileRep;
use App\Service\Foundation\BaseService;
use App\Service\Upload\UploadService;
use Illuminate\Http\Request;

class ClueUploadService extends BaseService
{
    protected $disk = 'uploads';

    protected $uploadService;

    protected $fileRep;

    protected $attachmentsField = [
        'attachment_type' => 'attachment_type',
        'newFileName' => 'filename',
        'originalName' => 'original_name',
        'file_path' => 'file_path',
        'extension' => 'file_extension',

    ];

    public function __construct(
        UploadService $uploadService,
        FileRep $fileRep
    )
    {
        parent::__construct();

        $this->uploadService = $uploadService;
        $this->fileRep = $fileRep;
    }

    /**
     * 上传线索附件
     * @param Request $request
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function clueUpload(Request $request, array $params)
    {
        try{
            if(! $request->isMethod('post')){
                throw new \Exception('request method must be post');
            }

            if(! isset($params['attachment_type'])){
                throw new \Exception('The attachment_type does not exists !');
            }

            $file = $request->file('file');
            $params['disk'] = $this->disk;
            $params['attachment_type'] = $params['attachment_type'] ?? 'word';

            // 上传文件
            $fileInfo = $this->uploadService->uploadFile($file, $params);

            // 存储文件信息
            $fileInfo['file_id'] = $this->saveAttachments($fileInfo);

            return ['fileInfo' => $fileInfo];

        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 存储附件信息
     * @param array $fileInfo
     * @return \App\Model\Foundation\type|int
     */
    protected function saveAttachments(array $fileInfo)
    {
        if(! $fileInfo) return 0;
        $data = combineArray($fileInfo, $this->attachmentsField);
        $data['json_data'] = json_encode($fileInfo);

        return $this->fileRep->saveAttachments($data);
    }
}