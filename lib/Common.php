<?php
/**
 * Created by PhpStorm.
 * User: 赵佳
 * Date: 2019/8/30
 * Time: 18:31
 */

namespace leapad\lib

class Common{

    /*** 二维数组查找一维数组中值是否
     * @param $array
     * @param callable $callback
     * @return array
     */
    public static function array_where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param $arrays
     * @param $array
     * @return bool
     */
    public static function arrra_exist($arrays, $array)
    {
        return (bool)array_filter($arrays, function ($_array) use ($array) {
            return !array_diff($array, $_array);
        });
    }

    /**
     * @param $array
     * @param $indexs
     * @param bool $justvalsplease
     * @return bool|mixed
     */
    public static function arrray_element($array, $indexs, $justvalsplease = false)
    {
        $newarray = false;
        //verificamos el array
        if (is_array($array) && count($array) > 0) {

            //verify indexs and get # of indexs
            if (is_array($indexs) && count($indexs) > 0) $ninds = count($indexs);
            else return false;

            //search for coincidences
            foreach (array_keys($array) as $key) {

                //index value coincidence counter.
                $count = 0;

                //for each index we search
                foreach ($indexs as $indx => $val) {

                    //if index value is equal then counts
                    if ($array[$key][$indx] == $val) {
                        $count++;
                    }
                }
                //if indexes match, we get the array elements :)
                if ($count == $ninds) {

                    //if you only need the vals of the first coincidence
                    //witch was my case by the way...
                    if ($justvalsplease) return $array[$key];
                    else $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }

}