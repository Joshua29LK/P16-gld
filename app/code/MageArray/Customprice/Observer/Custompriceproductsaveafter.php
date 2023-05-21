<?php

namespace MageArray\Customprice\Observer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Custompriceproductsaveafter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \MageArray\Customprice\Model\CsvpriceFactory
     */
    protected $_csvpriceFactory;

    /**
     * @var \MageArray\Customprice\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * [__construct description]
     *
     * @param \Magento\Framework\ObjectManagerInterface           $objectManager   [description]
     * @param \Magento\Framework\App\RequestInterface             $request         [description]
     * @param \MageArray\Customprice\Helper\Data                  $dataHelper      [description]
     * @param \MageArray\Customprice\Model\CsvpriceFactory        $csvpriceFactory [description]
     * @param \MageArray\Customprice\Model\ResourceModel\Csvprice $csvprice        [description]
     * @param \Magento\Framework\Filesystem\Io\File               $ioFile          [description]
     * @param \Magento\Framework\File\Csv                         $csv             [description]
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \MageArray\Customprice\Helper\Data $dataHelper,
        \MageArray\Customprice\Model\CsvpriceFactory $csvpriceFactory,
        \MageArray\Customprice\Model\ResourceModel\Csvprice $csvprice,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        $this->_dataHelper = $dataHelper;
        $this->_csvpriceFactory = $csvpriceFactory;
        $this->csvprice = $csvprice;
        $this->ioFile = $ioFile;
        $this->csv = $csv;
    }

    /**
     * [execute description]
     *
     * @param  \Magento\Framework\Event\Observer $observer [description]
     * @return [type]                                      [description]
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $active = $this->_dataHelper
            ->getStoreConfig('customprice/general/active');

        if ($active == 1) {
            $productdata = $observer->getEvent()->getProduct();

            $prdId = $productdata->getId();
            $csvFilePath = $this->_request->getParam('uploadedfile');
            $prod = $this->_request->getParam('product');
            //$isOneDimensional = $prod['is_one_dimensional'];

            $csvpricingModel = $this->_csvpriceFactory->create();
            $removeFile = $this->_request->getParam('remove_file');

            // Check if remove_file checked, or
            // new file uploaded for product , already stored file for product
            $this->removeCsvFiles($removeFile, $prdId);

            if ($csvFilePath) {
                foreach ($csvFilePath as $key1 => $value1) {
                    if ($value1 != "") {
                        $mediaDirectory = $this->_objectManager
                            ->get(\Magento\Framework\Filesystem::class)
                            ->getDirectoryRead(DirectoryList::MEDIA)
                            ->getAbsolutePath('csvfiles/allfiles');

                        $mediaNewDirectory = $this->_objectManager
                            ->get(\Magento\Framework\Filesystem::class)
                            ->getDirectoryRead(DirectoryList::MEDIA)
                            ->getAbsolutePath('csvfiles');

                        $orgfilename = $mediaDirectory . $value1;
                        /* $name = basename($value1); */
                        $arr = explode('/', $value1);
                        $name = end($arr);
                        $newfilename = $mediaNewDirectory . DIRECTORY_SEPARATOR . $prdId . DIRECTORY_SEPARATOR .
                        $key1 . DIRECTORY_SEPARATOR . $name;
                        if (!$this->ioFile->fileExists(
                            $mediaNewDirectory . DIRECTORY_SEPARATOR . $prdId . DIRECTORY_SEPARATOR . $key1
                        )) {
                            $this->ioFile->mkdir(
                                $mediaNewDirectory . DIRECTORY_SEPARATOR . $prdId . DIRECTORY_SEPARATOR . $key1,
                                0777,
                                true
                            );
                        }
                        $this->ioFile->mv($orgfilename, $newfilename);

                        $csvDelimiter = $productdata->getCsvDelimiter();

                        if ($csvDelimiter == "") {
                            $csvDelimiter = $this->_dataHelper
                                ->getStoreConfig('customprice/general/csv_delimiter');
                                
                            if ($csvDelimiter == "") {
                                $csvDelimiter = ",";
                            }
                        }
                        
                        if ($productdata->getApplyCsvType() == 'dimensional') {
                            $data = $this->onedimensionalCsvToArray($newfilename);
                            $data = $this->unsetNullValues($data);

                            //get min and max value
                            $minmax['minRow'] = min(array_keys($data));
                            $minmax['maxRow'] = max(array_keys($data));

                            $temp['pricesheet'] = $data;

                            $minmax['minPrice'] = min(array_values($data));
                            $minmax['maxPrice'] = max(array_values($data));
                            $temp['minmax'] = $minmax;

                            $val['row'] = array_keys($data);
                            $temp['vals'] = $val;

                            $resultJson = json_encode($temp);
                            $dataToSave = [];
                            $dataToSave['product_id'] = $prdId;
                            $dataToSave['csv_price'] = $resultJson;
                            $dataToSave['option_sku'] = $key1;
                            $dataToSave['file_name'] = DIRECTORY_SEPARATOR . $prdId . DIRECTORY_SEPARATOR .
                            $key1 . DIRECTORY_SEPARATOR . $name;
                            $dataToSave['f_name'] = $name;
                        } else {
                            $data = $this->csvToArray($newfilename, $csvDelimiter);
                            $data = $this->unsetNullValues($data);

                            //get min and max value
                            $minmax['minRow'] = min(array_keys($data));
                            $minmax['maxRow'] = max(array_keys($data));
                            $cols = array_keys($data[$minmax['minRow']]);
                            $minmax['minCol'] = min($cols);
                            $minmax['maxCol'] = max($cols);
                            $temp['pricesheet'] = $data;
                            $max = -9999999;
                            $min = '';
                            foreach ($data as $sub) {
                                $sub = $this->unsetNullValues($sub);
                                $tempMax = max($sub);
                                $tempMin = min($sub);

                                if ($min == '') {
                                    $min = $tempMin;
                                }
                                if ($tempMax > $max) {
                                    $max = $tempMax;
                                }
                                if ($tempMin < $min) {
                                    $min = $tempMin;
                                }
                            }
                            $minmax['minPrice'] = $min;
                            $minmax['maxPrice'] = $max;
                            $temp['minmax'] = $minmax;
                            $val['col'] = $cols;
                            $val['row'] = array_keys($data);
                            $temp['vals'] = $val;

                            $resultJson = json_encode($temp);

                            $dataToSave = [];
                            $dataToSave['product_id'] = $prdId;
                            $dataToSave['csv_price'] = $resultJson;
                            $dataToSave['option_sku'] = $key1;
                            $dataToSave['file_name'] = DIRECTORY_SEPARATOR . $prdId . DIRECTORY_SEPARATOR .
                            $key1 . DIRECTORY_SEPARATOR . $name;
                            $dataToSave['f_name'] = $name;
                        }

                        $collection = $csvpricingModel->getCollection()
                            ->addFieldToFilter('product_id', $prdId)
                            ->addFieldToFilter('option_sku', $key1)
                            ->getData();

                        if (empty($collection)) {
                            $csvpricingModel->setData($dataToSave);
                            try {
                                $csvpricingModel->save();
                            } catch (\Magento\Framework\Model\Exception $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\RuntimeException $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\Exception $e) {
                                $this->messageManager->addError($e->getMessage());
                                $this->messageManager
                                    ->addException(
                                        $e,
                                        __('Something went wrong while saving the data')
                                    );
                            }
                        } else {
                            //$csvpricingSaveModel1 = $this->_csvpriceFactory->create();
                            $csvpricingModel->load($collection[0]['id']);
                            $csvpricingModel->setProductId($dataToSave['product_id']);
                            $csvpricingModel->setCsvPrice($dataToSave['csv_price']);
                            $csvpricingModel->setOptionSku($dataToSave['option_sku']);
                            $csvpricingModel->setFileName($dataToSave['file_name']);
                            $csvpricingModel->setFName($dataToSave['f_name']);

                            try {
                                $csvpricingModel->save();
                            } catch (\Magento\Framework\Model\Exception $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\RuntimeException $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\Exception $e) {
                                $this->messageManager->addError($e->getMessage());
                                $this->messageManager
                                    ->addException(
                                        $e,
                                        __('Something went wrong while saving the data')
                                    );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * [unsetNullValues description]
     *
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function unsetNullValues($data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if ($key == '') {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

    /**
     * [removeCsvFiles description]
     *
     * @param  [type] $removeFile [description]
     * @param  [type] $prdId      [description]
     * @return [type]             [description]
     */
    public function removeCsvFiles($removeFile, $prdId)
    {
        if ($removeFile) {
            foreach ($removeFile as $removekey => $removevalue) {
                if ($removevalue == 1) {
                    $res = $this->csvprice->addCsvFilter($prdId, $removekey);
                    if (!empty($res)) {
                        $csvpricingId = $res['id'];
                        $csvpricingModel = $this->_csvpriceFactory->create();
                        $csvpricingModel->load($csvpricingId);
                        $csvpricingModel->delete();
                    }
                }
            }
        }
    }

    /**
     * [csvToArray description]
     *
     * @param  string $filename  [description]
     * @param  string $delimiter [description]
     * @return [type]            [description]
     */
    public function csvToArray($filename = '', $delimiter = ',')
    {
        $this->csv->setDelimiter($delimiter);
        $array = $this->csv->getData($filename);
        $header = null;
        $data = [];
        if (!empty($array)) {
            foreach ($array as $row) {
                if (!$header) {
                    $row = array_map('trim', $row);
                    $header = $row;
                } else {
                    $key = trim($row[0]);
                    unset($row[0]);
                    unset($header[0]);
                    $row = array_map('trim', $row);
                    $data[$key] = array_combine($header, $row);
                }
            }
        }
        return $data;
    }

    /**
     * [onedimensionalCsvToArray description]
     *
     * @param  string $filename  [description]
     * @param  string $delimiter [description]
     * @return [type]            [description]
     */
    public function onedimensionalCsvToArray($filename = '', $delimiter = ',')
    {
        if (!$this->ioFile->fileExists($filename)) {
            return false;
        }
        $csvArray = $this->csv->getData($filename);
        $data = [];
        foreach ($csvArray as $value) {
            $key = $value[0];
            $data[$key] = $value[1];
        }

        return $data;
    }
}
