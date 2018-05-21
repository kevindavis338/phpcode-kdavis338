<?php


class EPlanDataModelGroups {

	/**
	 *
	 * @var BourbonDB
	 */
	protected $db = null;

	function __construct(){
		$this->db = Bourbon::GetBourbonDB();
	}

	/**
	 *
	 * @param array() $group_codes
	 * @return array()
	 * @throws Exception
	 */
	public function GetExpressionTemplatesForGroups($group_codes){

		if (empty($group_codes)) return array();

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = sprintf('SELECT
		  map.eplan_group_code AS group_code,
		  eep.code,
		  eep.subscriber_code,
		  eep.name,
		  eep.description,
		  eep.`author`,
		  eep.`compliance_notification`
		FROM
		  eplan_expressions eep
		  JOIN `eplan_groups_expressions_map` map
		    ON map.`eplan_expression_code` = eep.`code`
		    AND map.`eplan_group_code` IN (%s)
		WHERE eep.is_deleted = "N"
		ORDER BY eep.name ASC ', $prep_stmt->GetPlaceholders(count($group_codes)));


		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameterArray($group_codes);

		return $prep_stmt->GetRecordsAsGroupedAssociativeArray('group_code', 'code');
	}

	/**
	 *
	 * @param array() $group_codes
	 * @return array()
	 * @throws Exception
	 */
	public function GetExpressionTemplatePagesForGroups($group_codes){

		if (empty($group_codes)) return array();

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = sprintf('SELECT
		  map.eplan_group_code AS group_code,
		  eep.code,
		  eep.eplan_expression_page_type_code AS page_type_code,
		  eep.eplan_subscriber_code AS subscriber_code,
		  eep.name,
		  eep.description,
		  eep.parameters_array,
		  eep.presentation_order
		FROM
		  eplan_expression_pages eep
		  JOIN `eplan_groups_expression_pages_map` map
		    ON map.`eplan_expression_page_code` = eep.`code`
		    AND map.`eplan_group_code` IN (%s)
		WHERE eep.is_deleted = "N"
		ORDER BY eep.presentation_order ASC', $prep_stmt->GetPlaceholders(count($group_codes)));


		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameterArray($group_codes);

		$keys[] = 'group_code';
		$keys[] = 'page_type_code';
		$keys[] = 'code';

		return $prep_stmt->GetRecordsAsMultiGroupedAssociativeArray($keys);
	}

	/**
	 *
	 * @return array()
	 * @throws Exception
	 */
	public function GetGroups(){

		$query = 'SELECT
		  `code`,
		  `name`,
		  `workflow_status_code`,
		  `created_at`,
		  `created_by_group_code`
		FROM
		  `eplan_groups`
		WHERE `is_deleted` != "Y" ';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		return $prep_stmt->GetRecordsAsKeyedAssociativeArray('code');
	}

	/**
	 *
	 * @param string $group_code
	 * @param string $expression_page_template_code
	 * @return boolean
	 */
	public function AddExpressionPageTemplateToGroup($group_code, $expression_page_template_code){
		$query = 'INSERT IGNORE INTO `eplan_groups_expression_pages_map` (
		  `eplan_group_code`,
		  `eplan_expression_page_code`,
		  `created_at`,
		  `created_by_group_code`
		)
		VALUES
		  (?, ?, NOW(), ?)';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($group_code);
		$prep_stmt->SetStringParameter($expression_page_template_code);
		$prep_stmt->SetStringParameter(BourbonUser::GetUserCode());

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $group_code
	 * @param string $expression_template_code
	 * @return boolean
	 */
	public function AddExpressionTemplateToGroup($group_code, $expression_template_code){
		$query = 'INSERT IGNORE INTO `eplan_groups_expressions_map` (
		  `eplan_group_code`,
		  `eplan_expression_code`,
		  `created_at`,
		  `created_by_group_code`
		)
		VALUES
		  (?, ?, NOW(), ?)';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($group_code);
		$prep_stmt->SetStringParameter($expression_template_code);
		$prep_stmt->SetStringParameter(BourbonUser::GetUserCode());

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $expression_page_template_code
	 * @return array
	 * @throws Exception
	 */
	public function GetGroupsForExpressionPageTemplate($expression_page_template_code){
		$query = 'SELECT
		  eg.`code`,
		  eg.`name`,
		  eg.`workflow_status_code`,
		  eg.`created_at`,
		  eg.`created_by_group_code`
		FROM
		  `eplan_groups_expression_pages_map` map
		  JOIN `eplan_groups` eg
		    ON eg.`code` = map.`eplan_group_code`
		    AND eg.`is_deleted` != "Y"
		WHERE map.`eplan_expression_page_code` = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($expression_page_template_code);

		return $prep_stmt->GetRecordsAsKeyedAssociativeArray('code');
	}

	/**
	 *
	 * @param string $subscriber_account_code
	 * @return array
	 * @throws Exception
	 */
	public function GetGroupsForSubscriberAccount($subscriber_account_code){
		$query = 'SELECT
		  g.`code`,
		  g.`name`,
		  g.`workflow_status_code`,
		  g.`created_at`,
		  g.`created_by_group_code`
		FROM
		  `eplan_groups_subscriber_accounts_map` map
		  JOIN `eplan_groups` g
		    ON g.`code` = map.`eplan_group_code`
		    AND g.`is_deleted` != "Y"
		WHERE map.`eplan_subscriber_account_code` = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($subscriber_account_code);

		return $prep_stmt->GetRecordsAsKeyedAssociativeArray('code');
	}

	/**
	 *
	 * @param string $subscriber_code
	 * @return array
	 * @throws Exception
	 */
	public function GetGroupsForSubscriber($subscriber_code){
		$query = 'SELECT
		  g.`code`,
		  g.`name`,
		  g.`workflow_status_code`,
		  g.`created_at`,
		  g.`created_by_group_code`
		FROM
		  `eplan_groups_subscribers_map` map
		  JOIN `eplan_groups` g
		    ON g.`code` = map.`eplan_group_code`
		    AND g.`is_deleted` != "Y"
		WHERE map.`eplan_subscriber_code` = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($subscriber_code);

		return $prep_stmt->GetRecordsAsKeyedAssociativeArray('code');
	}

	/**
	 *
	 * @param string $expression_template_code
	 * @return array
	 * @throws Exception
	 */
	public function GetGroupsForExpressionTemplate($expression_template_code){
		$query = 'SELECT
		  eg.`code`,
		  eg.`name`,
		  eg.`workflow_status_code`,
		  eg.`created_at`,
		  eg.`created_by_group_code`
		FROM
		  `eplan_groups_expressions_map` map
		  JOIN `eplan_groups` eg
		    ON eg.`code` = map.`eplan_group_code`
		    AND eg.`is_deleted` != "Y"
		WHERE map.`eplan_expression_code` = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($expression_template_code);

		return $prep_stmt->GetRecordsAsKeyedAssociativeArray('code');
	}

	/**
	 * Delete all groups which are not in the colletion
	 * @param string $expression_code
	 * @param array() $group_codes
	 * @return boolean
	 * @throws Exception
	 */
	public function DeleteGroupsNotInCollection($expression_code, $group_codes){

		if (empty($group_codes)){
			$group_codes[] = '__dummy';
		}

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = sprintf('DELETE
		FROM
		  `eplan_groups_expressions_map`
		WHERE `eplan_expression_code` = ?
		  AND `eplan_group_code` NOT IN (%s)', $prep_stmt->GetPlaceholders(count($group_codes)));


		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($expression_code);
		$prep_stmt->SetStringParameterArray($group_codes);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $subscriber_code
	 * @return array
	 * @throws Exception
	 */
	public function GetGroupsForExpressionPageTemplatesForSubscriber($subscriber_code){

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = 'SELECT
		  map.`eplan_expression_page_code` AS expression_page_template_code,
		  eg.`code`,
		  eg.`name`,
		  eg.`workflow_status_code`,
		  eg.`created_at`,
		  eg.`created_by_group_code`
		FROM
		  eplan_expression_pages eep
		  JOIN `eplan_groups_expression_pages_map` map
		    ON map.`eplan_expression_page_code` = eep.`code`
		  JOIN `eplan_groups` eg
		    ON eg.`code` = map.`eplan_group_code`
		    AND eg.`is_deleted` != "Y"
		WHERE eep.eplan_subscriber_code = ?
		  AND eep.is_deleted = "N"
		ORDER BY eg.name ASC ';

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($subscriber_code);

		return $prep_stmt->GetRecordsAsGroupedAssociativeArray('expression_page_template_code', 'code');
	}

	/**
	 *
	 * @param string $subscriber_code
	 * @return array
	 * @throws Exception
	 */
	public function GetGroupsForExpressionTemplatesForSubscriber($subscriber_code){

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = 'SELECT
		  map.`eplan_expression_code` AS expression_template_code,
		  eg.`code`,
		  eg.`name`,
		  eg.`workflow_status_code`,
		  eg.`created_at`,
		  eg.`created_by_group_code`
		FROM
		  eplan_expressions eep
		  JOIN `eplan_groups_expressions_map` map
		    ON map.`eplan_expression_code` = eep.`code`
		  JOIN `eplan_groups` eg
		    ON eg.`code` = map.`eplan_group_code`
		    AND eg.`is_deleted` != "Y"
		WHERE eep.subscriber_code = ?
		  AND eep.is_deleted = "N"
		ORDER BY eg.name ASC ';

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($subscriber_code);

		return $prep_stmt->GetRecordsAsGroupedAssociativeArray('expression_template_code', 'code');
	}

	/**
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function Add($data){
		$query = 'INSERT INTO `eplan_groups` (
		  `code`,
		  `name`,
		  `workflow_status_code`,
		  `created_at`,
		  `created_by_group_code`,
		  `is_deleted`
		)
		VALUES
		  (?, ?, ?, NOW(), ?, "N")';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$code = isset($data['code']) ? $data['code'] : BourbonUtilities::GetUUID();
		$prep_stmt->SetStringParameter($code);

		$prep_stmt->SetStringParameter($data['name']);
		$prep_stmt->SetStringParameter($data['workflow_status_code']);
		$prep_stmt->SetStringParameter(BourbonUser::GetUserCode());

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $group_code
	 * @param string $subscriber_code
	 * @return boolean
	 */
	public function AddSubscriberToGroup($group_code, $subscriber_code){
		$query = 'INSERT IGNORE INTO `eplan_groups_subscribers_map` (
		  `eplan_group_code`,
		  `eplan_subscriber_code`,
		  `created_at`
		)
		VALUES
		  (?, ?, NOW())';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($group_code);
		$prep_stmt->SetStringParameter($subscriber_code);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $group_code
	 * @param array $subscriber_codes
	 * @return boolean
	 */
	public function DeleteNonSelectedSubscribersForGroup($group_code, $subscriber_codes){

		if (empty($subscriber_codes)){
			$subscriber_codes[] = '_dummy';
		}

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = sprintf('DELETE
			FROM
			  `eplan_groups_subscribers_map`
			WHERE `eplan_group_code` = ?
			  AND `eplan_subscriber_code` NOT IN (%s)'
		, $prep_stmt->GetPlaceholders(count($subscriber_codes)));


		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($group_code);
		$prep_stmt->SetStringParameterArray($subscriber_codes);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $group_code
	 * @param string $subscriber_account_code
	 * @return boolean
	 */
	public function AddSubscriberAccountToGroup($group_code, $subscriber_account_code){
		$query = 'INSERT IGNORE INTO `eplan_groups_subscriber_accounts_map` (
		  `eplan_group_code`,
		  `eplan_subscriber_account_code`,
		  `created_at`
		)
		VALUES
		  (?, ?, NOW())';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($group_code);
		$prep_stmt->SetStringParameter($subscriber_account_code);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $group_code
	 * @param array $subscriber_account_codes
	 * @return boolean
	 */
	public function DeleteNonSelectedSubscriberAccountsForGroup($group_code, $subscriber_account_codes){

		if (empty($subscriber_account_codes)){
			$subscriber_account_codes[] = '_dummy';
		}

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = sprintf('DELETE
			FROM
			  `eplan_groups_subscriber_accounts_map`
			WHERE `eplan_group_code` = ?
			  AND `eplan_subscriber_account_code` NOT IN (%s)'
		, $prep_stmt->GetPlaceholders(count($subscriber_account_codes)));


		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($group_code);
		$prep_stmt->SetStringParameterArray($subscriber_account_codes);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $group_code
	 * @param array $subscriber_account_codes
	 * @return boolean
	 */
	public function DeleteNonSelectedGroupsForExpressionPageTemplate($expression_page_template_code, $group_codes){

		if (empty($group_codes)){
			$group_codes[] = '_dummy';
		}

		$prep_stmt = $this->db->GetPreparedStatement();

		$query = sprintf('DELETE
			FROM
			  `eplan_groups_expression_pages_map`
			WHERE `eplan_expression_page_code` = ?
			  AND `eplan_group_code` NOT IN (%s)'
		, $prep_stmt->GetPlaceholders(count($group_codes)));


		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($expression_page_template_code);
		$prep_stmt->SetStringParameterArray($group_codes);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function Delete($code){
		$query = 'UPDATE `eplan_groups`
			SET `is_deleted` = "Y"
			WHERE `code` = ?;';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($code);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function Update($data){
		$query = 'UPDATE `eplan_groups`
			SET `name` = ?,
			  `workflow_status_code` = ?
			WHERE `code` = ?;';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($data['name']);
		$prep_stmt->SetStringParameter($data['workflow_status_code']);

		$prep_stmt->SetStringParameter($data['group_code']);

		return $prep_stmt->Execute();
	}

	/**
	 *
	 * @param string $code
	 * @return array()
	 * @throws Exception
	 */
	public function GetGroupForCode($code){
		$query = 'SELECT
		  eg.`insert_sequence`,
		  eg.`code`,
		  eg.`name`,
		  eg.`workflow_status_code`,
		  eg.`created_at`
		FROM
		  `eplan_groups` eg
		WHERE eg.is_deleted != "Y"
		  AND eg.code = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($code);

		return $prep_stmt->GetRecord();
	}

	public function GetSubscribersForGroup($code){

		$query = 'SELECT
		  es.`code`,
		  es.`name`
		FROM
		  `eplan_groups_subscribers_map` map
		  JOIN `eplan_subscribers` es
		    ON es.`code` = map.`eplan_subscriber_code`
		    AND es.`is_deleted` != "Y"
		WHERE map.`eplan_group_code` = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($code);

		return $prep_stmt->GetRecordsAsAssociativeArray();
	}

	public function GetSubscriberAccountsForGroup($code){

		$query = 'SELECT
		  es.`code`,
		  CONCAT(es.`first_name`, " ", es.`last_name`) AS `name`
		FROM
		  `eplan_groups_subscriber_accounts_map` map
		  JOIN `eplan_subscriber_accounts` es
		    ON es.`code` = map.`eplan_subscriber_account_code`
		    AND es.`is_deleted` != "Y"
		WHERE map.`eplan_group_code` = ?';

		$prep_stmt = $this->db->GetPreparedStatement();

		$prep_stmt->SetQuery($query);

		$prep_stmt->SetStringParameter($code);

		return $prep_stmt->GetRecordsAsAssociativeArray();
	}
}