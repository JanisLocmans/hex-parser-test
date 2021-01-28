<?php

namespace App;

class UnpackAVL {
    const ZERO_BYTES = 8;
    const DATA_FIELD_LENGHT = 8;
    const CODEC_ID = 2;
    const NUMBER_OF_RECORDS = 2;

    //AVL DATA
    const TIMESTAMP = 16;
    const PRIORITY = 2;
    const LON = 8;
    const LAT = 8;
    const ALT = 4;
    const ANGLE = 4;
    const SATTELITES = 2;
    const SPEED = 4;
    const EVENT_IO_ID = 2;
    const TOTAL_IDs = 2;
    const N1 = 2;
    const N1V = 2;
    const N2 = 2;
    const N2V = 4;
    const N3 = 2;
    const N3V = 8;
    const N4 = 2;
    const N4V = 16;

    /**
     * @var string
     */
    private $data;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $output;

    /**
     * @var int
     */
    private $record_nr;

    /**
     * unpackAVL constructor.
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
        $this->position = 0;
        $this->output = [];
        $this->unpackPackage();
    }

    /**
     * @param string $data
     * @param int $position
     */
    private function unpackPackage()
    {
        $this->output['zero_bytes'] = $this->parseData(self::ZERO_BYTES);
        $this->output['data_field_length'] = $this->parseData(self::DATA_FIELD_LENGHT);
        $this->output['codec_id'] = $this->parseData(self::CODEC_ID);
        $this->output['number_of_records'] = $this->parseData(self::NUMBER_OF_RECORDS);
        $this->record_nr = hexdec( $this->output['number_of_records']);
        for ($x = 0; $x < $this->record_nr; $x++) {

            $this->output['avl_data'][$x]['n_values'] = [];

            $this->output['avl_data'][$x]['timestamp'] = $this->parseData(self::TIMESTAMP);
            $this->output['avl_data'][$x]['priority'] =  $this->parseData(self::PRIORITY);
            $this->output['avl_data'][$x]['longitude'] = $this->parseData(self::LON);
            $this->output['avl_data'][$x]['latitude'] = $this->parseData(self::LAT);
            $this->output['avl_data'][$x]['altitude'] = $this->parseData(self::ALT);
            $this->output['avl_data'][$x]['angle'] = $this->parseData(self::ANGLE);
            $this->output['avl_data'][$x]['sattelites'] = $this->parseData(self::SATTELITES);
            $this->output['avl_data'][$x]['speed'] =  $this->parseData(self::SPEED);
            $this->output['avl_data'][$x]['event_io_id'] = $this->parseData(self::EVENT_IO_ID);
            $this->output['avl_data'][$x]['total_ids'] = $this->parseData(self::TOTAL_IDs);


            $this->output['avl_data'][$x]['n_values'] = $this->parseNValues(self::N1V, self::N1, $this->output['avl_data'][$x]['n_values']);
            $this->output['avl_data'][$x]['n_values'] = $this->parseNValues(self::N2V, self::N2, $this->output['avl_data'][$x]['n_values']);
            $this->output['avl_data'][$x]['n_values'] = $this->parseNValues(self::N3V, self::N3, $this->output['avl_data'][$x]['n_values']);
            $this->output['avl_data'][$x]['n_values'] = $this->parseNValues(self::N4V, self::N4, $this->output['avl_data'][$x]['n_values']);
        }
    }

    /**
     * @param int $position
     * @return int
     */
    private function parseData(int $part_position)
    {
        $part = substr($this->data, $this->position, $part_position);
        $this->position += strlen($part);
        return  hexdec($part);
    }

    /**
     * @param int $position_size
     * @param int $type
     * @param array $output
     * @return array
     */
    private function parseNValues(int $position_size, int $type, array $output)
    {
        $count = $this->parseData($type);
        if($count > 0) {
            for($y = 0; $y < $count; $y++) {
                $output[] = [
                    'id'  =>  $this->parseData($type),
                    'value' => $this->parseData($position_size)
                ];
            }
        }

        return $output;
    }

    /**
     * @return array
     */
    public function retrieveData() {
        return    $this->output;
    }

}
