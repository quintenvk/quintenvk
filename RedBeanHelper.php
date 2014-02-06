<?php
namespace quintenvk;

use \RedBean_Facade as R;
/**
 * a class to help with nestedset functionality for redbean.
 */
class RedBeanHelper {
	public static function getBean_ids($table, $parentBean, $withParent = true) {
		$where = $withParent ? 'left_id >= ? AND right_id <= ?' : 'left_id > ? AND right_id < ?';
		return R::getCol('SELECT id FROM '.$table. ' WHERE '. $where, array((int)$parentBean->left_id, (int)$parentBean->right_id));
	}

	public static function isDescendantOf($parent, $child) {
		if($child->left_id >= $parent->left_id && $child->right_id <= $parent->right_id) {
			return true;
		}
		return false;
	}

	public static function getDescendants($table, $parentBean, $withParent = true) {
		$where = $withParent ? 'left_id >= ? AND right_id <= ?' : 'left_id > ? AND right_id < ?';
		return R::find($table, $where, array($parentBean->left_id, $parentBean->right_id));
	}

	public static function addChild($table, &$parentBean, &$childBean) {

		//first, we increase all the leftid's for nodes on the right of the parent by 2 (to make room for 1 left and 1 right_id.)
		//we also increase the right_id of the parentbean itself, so that it has room for an additional bean.
		R::begin();
		try {

			R::exec('UPDATE '.$table.' SET left_id = left_id+2 WHERE left_id > ?', array($parentBean->right_id));
			R::exec('UPDATE '.$table.' SET right_id = right_id+2 WHERE right_id >= ?', array($parentBean->right_id));
			$parentBean->right_id += 2;

			$childBean->parent = $parentBean;
			$childBean->right_id = $parentBean->right_id-1;
			$childBean->left_id = $parentBean->right_id-2;

			R::store($childBean);
			R::commit();

		} catch(Exception $e) {
	        R::rollback();
	        throw $e;
	    }
	}

	public static function removeChild($table, $parentBean, $bean) {
		//we still need 2 separate queries because the parent's left_id can't be updated...
		R::begin();
		try {

			R::trash($bean);
			R::exec('UPDATE '.$table.' SET left_id = left_id-2 WHERE left_id > ?', array($bean->right_id));
			R::exec('UPDATE '.$table.' SET right_id = right_id-2 WHERE right_id >= ?', array($bean->right_id));
			$parentBean->right_id -= 2;
			R::commit();

		} catch(Exception $e) {
	        R::rollback();
	        throw $e;
	    }
	}

}