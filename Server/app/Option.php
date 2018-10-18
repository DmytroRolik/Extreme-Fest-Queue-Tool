<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{

    static public function getDateStart()
    {
        return Option::getValue("dateStart");
    }

    static public function getDateEnd()
    {
        return Option::getValue("dateEnd");
    }

    static public function setDateStart($date)
    {
        Option::setValue("dateStart", $date);
    }

    static public function setDateEnd($date)
    {
        Option::setValue("dateEnd", $date);
    }

    /**
     * Возвращает массив дат мероприятия,
     * вычисляя их из начальной и конечной дат
     * @return array
     */
    static public function getDaysArray()
    {
        $dateStartString = self::getDateStart();
        $dateEndString = self::getDateEnd();

        $dateStart = date_create_from_format('d.m.Y', $dateStartString);
        $dateEnd = date_create_from_format('d.m.Y', $dateEndString);

        while ($dateStart->getTimestamp() <= $dateEnd->getTimestamp()) {

            $result[] = clone $dateStart;
            date_add($dateStart, date_interval_create_from_date_string('1 days'));
        }

        return $result;
    }

    // На сколько осуществляется перенос при опоздании
    static public function getQueueOffset()
    {
        return intval(Option::getValue("queueOffset"));
    }
    static public function setQueueOffset($offset)
    {
        Option::setValue("queueOffset", $offset);
    }

    // Максимальное число опозданий
    static public function getMaxLateCount()
    {
        return intval(Option::getValue("maxLateCount"));
    }
    static public function setMaxLateCount($offset)
    {
        Option::setValue("maxLateCount", $offset);
    }

    // Получить записать значение
    static public function getValue($optionName)
    {

        return Option::where('name', $optionName)->first()->value;
    }

    static public function setValue($optionName, $value)
    {

        $option = Option::where('name', $optionName)->first();
        $option->value = $value;
        $option->save();
    }
}
