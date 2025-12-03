<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = "setting";

    protected $fillable = [
        'name', 'label', 'type', 'value', 'option', 'attr'
    ];

    public static function getTypes()
    {
        return [
            0 => 'Text', 1 => 'Number', 2 => 'Select',
            3 => 'Radio', 4 => 'Checkbox', 5 => 'Textarea',
            6 => 'File', 7 => 'Date', 8 => 'JSON'
        ];
    }

    public function getOptions()
    {
        try {
            $options = [];
            $rows = explode('||', str_replace("\n", ' ', $this->option));

            foreach ($rows as $row) {
                $pair = explode(':', trim($row));

                if (count($pair) == 2) {
                    $options[$pair[0]] = $pair[1];
                } else {
                    $options[$pair[0]] = $pair[0];
                }
            }
            return $options;

        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Render input field
     */
    public function getField()
    {
        $name = $this->name;
        $label = $this->label;
        $value = $this->value;
        $attr = $this->attr ?? '';

        // SELECT
        if ($this->type == 2) {
            $html = "<div class='form-floating form-floating-outline'>
                        <select class='form-select' id='$name' name='$name' $attr>";
            foreach ($this->getOptions() as $key => $opt) {
                $selected = ($value == $key) ? 'selected' : '';
                $html .= "<option value='$key' $selected>$opt</option>";
            }
            $html .= "</select><label for='$name'>$label</label></div>";
            return $html;
        }

        // RADIO & CHECKBOX
        if (in_array($this->type, [3, 4])) {
            $type = ($this->type == 3) ? 'radio' : 'checkbox';
            $options = $this->getOptions();

            $html = "<div class='row g-1'>";
            if($this->type == 4){
                $value = json_decode($value);
            }
            foreach ($options as $key => $opt) {
                $checked = ($value == $key) ? 'checked' : '';
                if($value && $this->type == 4 && in_array(trim($key),$value)){
                    $checked =  'checked';
                }
                $html .= "
                    <div class='col-6'>
                        <div class='input-group'>
                            <div class='input-group-text'>
                                <input type='$type' name='$name' value='$key' $checked class='form-check-input'>
                            </div>
                            <label class='input-group-text'>$opt</label>
                        </div>
                    </div>";
            }
            $html .= "</div>";
            return $html;
        }

        // JSON
        // if ($this->type == 8) {
        //     $json = json_decode($value, true) ?: [];
        //     $html = "<div class='json_fields'>";
        //     $i = 0;

        //     foreach ($json as $key => $val) {
        //         $html .= "
        //             <div class='row mb-2'>
        //                 <div class='col'><input class='form-control json-key' value='$key'></div>
        //                 <div class='col-1 text-center'> = </div>
        //                 <div class='col'><input class='form-control json-value' value='$val'></div>
        //             </div>";
        //         $i++;
        //     }

        //     $html .= "</div>
        //         <button data-field='$name' data-count='$i'
        //                 class='btn btn-sm btn-outline-primary add-json-row'>
        //                 Add Row
        //         </button>";
        //     return $html;
        // }
        if ($this->type == 8) {
                $value = [];
                try {
                    $value = ($this->value != null) ? json_decode($this->value,true) : $value;
                } catch (\Throwable $th) {
                }
                $html = '<div class="border border-2 p-3 rounded-3" style="overflow-y: scroll;max-height: 500px;">';
                $html .= '<div class="json_fields" >';
                $index = 0;
                foreach($value as $k => $v){
                    $html .= '<div class="row mb-3">
                                <div class="col">
                                    <input type="text" id="key-'. $this->name .'-'. $index .'" data-value="value-'. $this->name .'-'. $index .'" class="form-control phone-mask '. $this->name .'-key" value="'. $k .'">
                                </div>
                                <div class="col-1 align-content-around p-0 m-0 text-center"> = </div>
                                <div class="col">
                                    <input type="text" id="value-'. $this->name .'-'. $index .'" class="form-control phone-mask" value="'. $v .'">
                                </div>
                            </div>';
                            $index++;
                }
                // dump($this);
                $html .= '</div>';
                $html .= '<button data-field="'. $this->name .'" data-count="'. count($value) .'" id="add_key_value_pair" type="button" class="btn btn-sm btn-outline-primary waves-effect">Add Key Value Pair</button>';
                $html .= '</div>';
                return $html;
            }
        // TEXTAREA
        if ($this->type == 5) {
            return "<textarea id='$name' name='$name' class='form-control' $attr>$value</textarea>";
        }

        // DEFAULT
        $type = strtolower(self::getTypes()[$this->type] ?? 'text');
        return "
            <div class='form-floating form-floating-outline'>
                <input type='$type' id='$name' name='$name' value='$value' class='form-control' $attr>
                <label for='$name'>$label</label>
            </div>";
    }

    public function getButtons()
    {
        return "
            <button class='btn btn-success save-setting-value me-2' data-field='{$this->name}' data-type='{$this->type}'>Save</button>
            <button class='btn btn-info edit-setting' data-id='{$this->id}'>Edit</button>
        ";
    }


    public static function getValue($name,$callback=null){
        $value =  self::where('name',$name)->value('value');
        if($value){
            return $value;
        }
        return $callback;
    }

}
