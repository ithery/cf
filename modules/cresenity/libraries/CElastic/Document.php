<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 18, 2017, 9:05:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Document {

    protected $index;
    protected $document_type;

    /*
     * @var CElastic
     */
    protected $elastic;
    /*
     * @var Elasticsearch\Client
     */
    protected $client;
    /*
     * @var CElastic_Indices
     */
    protected $indices;

    public function __construct(CElastic $elastic, $index, $document_type) {
        $this->elastic = $elastic;
        $this->client = $elastic->client();
        $this->index = $index;
        $this->document_type = $document_type;
        $this->indices = $this->elastic->indices($this->index, $this->document_type);
    }

    public function bulk($query, $index_field_name = "") {
        $params = ['body' => []];

        $i = 0;

        $error = 0;
        $error_message = "";

        $db = CDatabase::instance();
        $results = $db->query($query);
        $list_field = $results->list_fields();

        $mapping = $this->indices->get_mapping();

        $properties = carr::path($mapping, $this->index . '.mappings.' . $this->document_type . '.properties');

        $result = array();
        foreach ($results as $key => $value) {
            $row = (array) $value;

            $id = $row[$list_field[0]];
            if (strlen($index_field_name) > 0) {
                $id = $row[$index_field_name];
            }

            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                    '_type' => $this->document_type,
                    '_id' => $id
                ]
            ];


            foreach ($row as $key => $val) {
                //we transform the mapping date to elastic date format
                if (carr::path($properties, $key . '.type') == 'date') {
                    $row[$key] = date_format(date_create($val), "Y-m-d") . "T" . date_format(date_create($val), "H:i:s") . ".000Z";
                }
            }

            $params['body'][] = $row;

            $i++;

            if ($i == 1000) {
                $i = 0;

                $result = $this->client->bulk($params);

                $params = ['body' => []];

                if (isset($result["items"])) {
                    foreach ($result["items"] as $key => $value) {
                        if (isset($value["index"]["status"])) {
                            if ($value["index"]["status"] != 200 && $value["index"]["status"] != 201) {
                                $error++;

                                $error_message .= $value["index"]["_id"] . ": ";

                                if (isset($value["index"]["error"]["caused_by"]["reason"])) {
                                    $error_message .= $value["index"]["error"]["caused_by"]["reason"] . ", ";
                                }

                                if (isset($value["index"]["error"]["reason"])) {
                                    $error_message .= $value["index"]["error"]["reason"];
                                }
                            }
                        }
                    }
                }

                if ($error > 0) {
                    $params = ['body' => []];

                    break;

                    throw new Exception('Elastic Error:' . $error_message);
                }
            }
        }

        if (!empty($params['body']) && $error == 0) {
            $result = $this->client->bulk($params);

            if (isset($result["items"])) {
                foreach ($result["items"] as $key => $value) {
                    if (isset($value["index"]["status"])) {
                        if ($value["index"]["status"] != 200 && $value["index"]["status"] != 201) {
                            $error++;

                            $error_message .= $value["index"]["_id"] . ": ";

                            if (isset($value["index"]["error"]["caused_by"]["reason"])) {
                                $error_message .= $value["index"]["error"]["caused_by"]["reason"] . ", ";
                            }

                            if (isset($value["index"]["error"]["reason"])) {
                                $error_message .= $value["index"]["error"]["reason"];
                            }
                        }
                    }
                }
            }

            if ($error > 0) {
                throw new Exception('Elastic Error:' . $error_message);
            }
        }
        return $result;
    }

    public function delete($id) {
        $params = array();
        $params['index'] = $this->index;
        $params['type'] = $this->type;
        $params['id'] = $this->id;

        try {
            $this->client->delete($params);
        } catch (Exception $e) {
            throw new Exception($this->index . " - Delete Document Failed", 1);
        }
    }

}
