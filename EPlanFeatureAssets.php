<?php
/**
 * EPlanFeatureAssets.php
 *
 * File_Description
 *
 * @subpackage EPlanFeature
 * @author Kevin Davis

 */

#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------



require_once BOURBON_PATH_FEATURES . '/EPlan/EPlanFeature.php';

class EPlanFeatureAssets extends EPlanFeature {

	/* ----- CONSTRUCTOR ----- */

	/**
	 * Constructor - sets up the feature
	 */
	function EPlanFeatureAssets() {
		parent::EPlanFeature(); // PHP doesn't automatically call our parent's constructor so we have to manually do so
	}

	/* ----- PROVIDERS AND TASKS (MAIN ENTRY POINTS) ----- */

	/**
	 * Executes Feature logic and returns VIEW as a string
	 * @param array() $parameter_array
	 * @return string - content for the requested view
	 */
	function Provide($parameter_array) {

		$view_data = $this->GetStandardViewData();
		$view_data = array_merge($parameter_array, $view_data);

		// check what action should be executed
		switch ($this->GetProcess()){

			// personal

			case 'refresh_personal_asset_overview':
				return $this->RunRefreshPersonalAsset($view_data);

			case 'save_personal_asset_form':
				return $this->RunSavePersonalAssetForm($view_data);

			case 'display_personal_asset_form':
				return $this->RunDisplayPersonalAssetForm($view_data);

			case 'delete_personal_asset':
				return $this->RunDeletePersonalAsset($view_data);

				// liquid
			case 'update_asset_allocation_for_percent':
				return $this->UpdateAssetAllocationForPercent($view_data);

			case 'update_asset_allocation_for_money':
				return $this->UpdateAssetAllocationForMoney($view_data);

			case 'refresh_liquid_asset_overview':
				return $this->RunRefreshLiquidAsset($view_data);

			case 'delete_liquid_asset':
				return $this->RunDeleteLiquidAsset($view_data);

			case 'save_liquid_asset_form':
				return $this->RunSaveLiquidAssetForm($view_data);

			case 'display_liquid_asset_form':
				return $this->RunDisplayLiquidAssetForm($view_data);

			case 'display_structure_specific_form_fields_for_liquid_asset_form':
				return $this->RunDisplayStructureSpecificFormFieldsForLiquidAssetForm($view_data);

			case 'display_primary_insured_for_life_insurance':
				return $this->RunDisplayPrimaryInsuredForLifeInsurance($view_data);

			case 'display_annuitants':
				return $this->RunDisplayAnnuitants($view_data);


				// business

			case 'refresh_business_asset_overview':
				return $this->RunRefreshBusinessAsset($view_data);

			case 'delete_business_asset':
				return $this->RunDeleteBusinessAsset($view_data);

			case 'display_business_asset_form':
				return $this->RunDisplayBusinessAssetForm($view_data);

			case 'save_business_asset_form':
				return $this->RunSaveBusinessAssetForm($view_data);

				// general

			case 'update_all':
				return $this->RunUpdateAssetFields($view_data);

			case 'display_income_tax_treatment':
				return $this->RunDisplayIncomeTaxTreatment($view_data);

			case 'display_ownership_form_field_for_asset_form':
				return $this->RunDisplayOwnershipForAssetForm($view_data);

			default:
				return $this->RunDisplayAssetsOverview($view_data);
		}
	}

	protected function RunDisplayAssetsOverview($view_data){

		$view_data['overview_data_personal'] = $this->DoGetPersonalAssetOverviewData($view_data);

		$view_data['overview_data_liquid'] = $this->DoGetLiquidAssetOverviewData($view_data);

		$view_data['overview_data_business'] = $this->DoGetBusinessAssetOverviewData($view_data);

		return $this->IncludeMy('views/assets/overview.php', $view_data);
	}

	/**
	 * Executes Feature task
	 * @param array() $parameter_array
	 * @return mixed
	 */
	function ExecuteTask($parameter_array) {}

	/**
	 * Executes Feature service
	 * @param array() $parameter_array
	 * @return mixed
	 */
	function RequestService($parameter_array) {
		// determine service
		$requested_service= !empty($parameter_array) ? $parameter_array['service'] : '';

		switch ($requested_service){
		}
	}

	/* ----- SERVICES ----- */




	/* ----- TASKS ----- */


	/* ----- INSTALLER ----- */
	public function Install($version) {}



	/* ---------------------------------------------------- */
	/* ---------------------------------------------------- */
	/* -------------------- PROCESS ----------------------- */
	/* ---------------------------------------------------- */


	protected function RunUpdateAssetFields($view_data){

		switch ($view_data['asset_structure_code']){

			case 'personal_asset':

				$value_for_update = $view_data['fair_market_value'];

				// cost_basis
				$tag_id = $this->element_code . '_cost_basis';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				// fair_market_value_at_death - client_only
				$tag_id = $this->element_code . '_client_only_fair_market_value_at_death';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				// fair_market_value_at_death - client_spouse_only
				$tag_id = $this->element_code . '_client_spouse_only_fair_market_value_at_death';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				// fair_market_value_at_death - client_and_spouse
				$tag_id = $this->element_code . '_client_and_spouse_fair_market_value_at_death';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				return json_encode($json_array);

			case 'business_asset':

				$value_for_update = $view_data['fair_market_value'];

				// cost_basis
				$tag_id = $this->element_code . '_business_asset_cost_basis';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				// fair_market_value_at_death - client_only
				$tag_id = $this->element_code . '_business_asset_client_only_fair_market_value_at_death';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				// fair_market_value_at_death - client_spouse_only
				$tag_id = $this->element_code . '_business_asset_client_spouse_only_fair_market_value_at_death';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				// fair_market_value_at_death - client_and_spouse
				$tag_id = $this->element_code . '_business_asset_client_and_spouse_fair_market_value_at_death';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				return json_encode($json_array);


			case 'immediate_annuity':
			case 'cash_value_life_insurance':
			case 'deferred_annuity':
			case 'custom_income_asset':

				$value_for_update = $view_data['cash_value'];

				$tag_id = $this->element_code . '_cost_basis';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				return json_encode($json_array);

			case 'cash_account':
			case 'retail_account':
			case 'traditional_ira':
			case 'roth_ira':
			case 'traditional_401k':

				$value_for_update = $view_data['fair_market_value'];

				$tag_id = $this->element_code . '_cost_basis';
				$json_update_form['update_callback'] = BourbonUtilities::JSONReplaceTagInnerHTML($tag_id, $value_for_update);
				$json_array[] = $json_update_form;

				return json_encode($json_array);
		}

		$message_content = 'We cannot update right now, please try again';

		$update_id = $this->element_code . '_message_content_area';
		$json_update_form['update_id'] = $update_id;
		$json_update_form['update_html'] = BourbonNotifier::GetRenderedMessage('error', $message_content);
		$json_array[] = $json_update_form;

		return json_encode($json_array);
	}


	/**
	 * Display asset form
	 * @param array() $view_data
	 * @return string
	 * @throws Exception
	 */
	protected function RunDisplayLiquidAssetForm($view_data){

		$asset_code = @$view_data['asset_code'];
		$client_code = $view_data['client'];
		$client_data_set_code = $view_data['client_data_set_code'];

		/* ----- LOAD BASIC FORM DATA ----- */
		$view_data['classification_types'] = $this->DoGetAssetClassificationTypes();
		$view_data['structure_types'] = $this->DoGetAssetStructureTypes();

		$all_people = EPlanModel::GetPeopleForClient($client_code);
		$all_trusts = EPlanModel::GetTrustsForClientDataSet($client_data_set_code);
		$all_entities = EPlanModel::GetEntitiesForClientDataSet($client_data_set_code);

		if (!empty($asset_code)){
			$asset_data = $this->DoGetAssetInformation($view_data['asset_code']);

			if (!isset($view_data['asset_name'])) $view_data['asset_name'] = $asset_data['name'];
			if (!isset($view_data['asset_classification_code'])) $view_data['asset_classification_code'] = $asset_data['eplan_asset_classification_code'];
			if (!isset($view_data['asset_structure_code'])) $view_data['asset_structure_code'] = $asset_data['eplan_asset_structure_code'];
			if (!isset($view_data['ownership_type'])) $view_data['ownership_type'] = $asset_data['eplan_ownership_type_code'];

			if (!isset($view_data['fair_market_value'])) $view_data['fair_market_value'] = $asset_data['fair_market_value'];
			if (!isset($view_data['fair_market_value'])) $view_data['fair_market_value_explanation'] = $asset_data['fair_market_value_explanation'];

			if (!isset($view_data['cost_basis'])) $view_data['cost_basis'] = $asset_data['cost_basis'];
			if (!isset($view_data['cost_basis_explanation'])) $view_data['cost_basis_explanation'] = $asset_data['cost_basis_explanation'];

			if (!isset($view_data['annual_cash_flow_in'])) $view_data['annual_cash_flow_in'] = $asset_data['annual_cash_flow_in'];
			if (!isset($view_data['cash_value'])) $view_data['cash_value'] = $asset_data['cash_value'];
			if (!isset($view_data['taxation_code'])) $view_data['taxation_code'] = $asset_data['eplan_taxation_code'];
			if (!isset($view_data['policy_type_code'])) $view_data['policy_type_code'] = $asset_data['eplan_policy_type_code'];
			if (!isset($view_data['lump_sum_death_benefit'])) $view_data['lump_sum_death_benefit'] = $asset_data['lump_sum_death_benefit'];
			if (!isset($view_data['annual_income_death_benefit'])) $view_data['annual_income_death_benefit'] = $asset_data['annual_income_death_benefit'];

			if (!isset($view_data['insured_person_code_1'])) $view_data['insured_person_code_1'] = $asset_data['insured_person_code_1'];
			if (!isset($view_data['insured_person_code_2'])) $view_data['insured_person_code_2'] = $asset_data['insured_person_code_2'];

			if (!isset($view_data['people_with_ownership'])) $view_data['people_with_ownership'] = EPlanModel::GetPeopleWithOwnershipForAsset($asset_code);
			if (!isset($view_data['trusts_with_ownership'])) $view_data['trusts_with_ownership'] = $this->DoGetTrustsWithOwnershipForAsset($asset_code);
			if (!isset($view_data['entities_with_ownership'])) $view_data['entities_with_ownership'] = $this->DoGetEntitiesWithOwnershipForAsset($asset_code);

			// extract all fmv at death values
			if (!empty($asset_data['fmv_at_death'])){
				foreach ($asset_data['fmv_at_death'] as $fmv_at_death_type_code => $values){
					if (!isset($view_data['fair_market_value_at_death'][$fmv_at_death_type_code])) $view_data['fair_market_value_at_death'][$fmv_at_death_type_code] = $values['fair_market_value'];
					if (!isset($view_data['fair_market_value_at_death_explanation'][$fmv_at_death_type_code])) $view_data['fair_market_value_at_death_explanation'][$fmv_at_death_type_code] = $values['fair_market_value_explanation'];
				}
			}
		}

		// get specific form fields for structure
		$view_data['structure_specific_form_field_content'] = $this->RunDisplayStructureSpecificFormFieldsForLiquidAssetForm($view_data);

		/* ----- SET DEFAULT DATA ----- */

		// assemble all people for ownership
		if (!isset($view_data['people_with_ownership'])) $view_data['people_with_ownership'] = array();
		$view_data['people_without_ownership'] = array_diff_key($all_people, $view_data['people_with_ownership']);

		// assemble all trusts for ownership
		if (!isset($view_data['trusts_with_ownership'])) $view_data['trusts_with_ownership'] = array();
		$view_data['trusts_without_ownership'] = array_diff_key($all_trusts, $view_data['trusts_with_ownership']);

		// assemble all entities for ownership
		if (!isset($view_data['entities_with_ownership'])) $view_data['entities_with_ownership'] = array();
		$view_data['entities_without_ownership'] = array_diff_key($all_entities, $view_data['entities_with_ownership']);

		// get ownership form field content
		$view_data['ownership_form_field_content'] = $this->RunDisplayOwnershipForAssetForm($view_data);

		return $this->IncludeMy('views/assets/asset_form_liquid.php', $view_data);
	}

	protected function PrepareLiquidAssetData($asset_code, $asset_value, &$view_data){

		if (isset($view_data['liquid_asset_type_data'])){
			$_data = $view_data['liquid_asset_type_data'];

			$asset_types = EPlanModel::GetLiquidAssetTypes();

			foreach ($asset_types as $type_code => $name){

				if (!isset($_data[$type_code])) continue;

				if (!empty($_data[$type_code.'_percent'])){

					$asset_value = ereg_replace("[^0-9.]", "", $asset_value);

					$money_value = $asset_value * $_data[$type_code.'_percent'] / 100;
					$view_data['liquid_asset_type_data'][$type_code.'_money'] = EPlanFeature::FormatMoney($money_value);
				}
				elseif (empty($_data[$type_code.'_percent']) && !empty($_data[$type_code.'_money']) && $asset_value > 0){
					$view_data['liquid_asset_type_data'][$type_code.'_percent'] = number_format($_data[$type_code.'_money'] / $asset_value * 100, 2);
				}
			}
		}
		else {
			$_data = EPlanModel::GetLiquidAssetTypesForAsset($asset_code);

			$view_data['liquid_asset_type_data'] = array();

			foreach ($_data as $type_code => $percent){
				$view_data['liquid_asset_type_data'][$type_code] = 'Y';

				$type_form_field = $type_code . '_percent';
				$view_data['liquid_asset_type_data'][$type_form_field] = $percent;

				$type_form_field = $type_code . '_money';
				$money_value = $asset_value * $percent / 100;
				$view_data['liquid_asset_type_data'][$type_form_field] = EPlanFeature::FormatMoney($money_value);
			}
		}
	}

	protected function RunDisplayStructureSpecificFormFieldsForLiquidAssetForm($view_data){
		$structure_type = @$view_data['asset_structure_code'];
		$asset_code = @$view_data['asset_code'];
		$client_code = @$view_data['client'];
		$client_data_set_code = @$view_data['client_data_set_code'];

		$view_data['tooltip'] = self::GetToolTip('collateral_assignment_at_death');

		if (empty($structure_type)) return '&nbsp;';

		if (empty($client_code)) throw new Exception($this->GetSystemMessage('RunDisplayStructureSpecificFormFieldsForLiquidAssetForm-invalid_param'));

		// get ownership form field content
		$view_data['ownership_form_field_content'] = $this->RunDisplayOwnershipForAssetForm($view_data);

		// get all ownership types
		$view_data['ownership_types'] = $this->DoGetOwnershipTypes($structure_type);

		/* ----- CASH ACCOUNT ----- */
		if ($structure_type == 'cash_account'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['fair_market_value'], $view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormWithTriggersForLifeInsurance($view_data);

			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] =  $this->IncludeMy('views/assets/liquid_asset_type_form_fixed_only.php', $view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_cash_account.php', $view_data);
		}

		/* ----- RETAIL ACCOUNT ----- */
		if ($structure_type == 'retail_account'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['fair_market_value'], $view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormWithTriggersForLifeInsurance($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_retail_account.php', $view_data);
		}

		/* ----- CASH VALUE LIFE INSURANCE ----- */
		if ($structure_type == 'cash_value_life_insurance'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['cash_value'], $view_data);

			$view_data['taxation_items'] = $this->DoGetTaxations('cash_value_life_insurance');
			$view_data['policy_type_items'] = $this->DoGetPolicyTypes('cash_value_life_insurance');

			// determine primary insured people
			$view_data['primary_insured_content'] = $this->RunDisplayPrimaryInsuredForLifeInsurance($view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_cash_value_life_insurance.php', $view_data);
		}

		/* ----- DEFERRED ANNUITY ----- */
		if ($structure_type == 'deferred_annuity'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['cash_value'], $view_data);

			$view_data['taxation_items'] = $this->DoGetTaxations('deferred_annuity');
			$view_data['policy_type_items'] = $this->DoGetPolicyTypes('deferred_annuity');

			// determine annuitants
			$view_data['annuitants_content'] = $this->RunDisplayAnnuitants($view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_deferred_annuity.php', $view_data);
		}

		/* ----- IMMEDIATE ANNUITY ----- */
		if ($structure_type == 'immediate_annuity'){
			$this->PrepareLiquidAssetData($asset_code, $view_data['cash_value'], $view_data);

			$view_data['taxation_items'] = $this->DoGetTaxations('immediate_annuity');
			$view_data['policy_type_items'] = $this->DoGetPolicyTypes('immediate_annuity');

			// determine annuitants
			$view_data['annuitants_content'] = $this->RunDisplayAnnuitants($view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_immediate_annuity.php', $view_data);
		}

		/* ----- ROTH IRA ----- */
		if ($structure_type == 'roth_ira'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['fair_market_value'], $view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_roth_ira.php', $view_data);
		}

		/* ----- TRADITIONAL IRA ----- */
		if ($structure_type == 'traditional_ira'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['fair_market_value'], $view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_traditional_ira.php', $view_data);
		}

		/* ----- TRADITIONAL 401K ----- */
		if ($structure_type == 'traditional_401k'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['fair_market_value'], $view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_traditional_401k.php', $view_data);
		}

		/* ----- CUSTOM INCOME ASSET ----- */
		if ($structure_type == 'custom_income_asset'){

			$this->PrepareLiquidAssetData($asset_code, $view_data['cash_value'], $view_data);

			$view_data['taxation_items'] = $this->DoGetTaxations('custom_income_asset');
			$view_data['policy_type_items'] = $this->DoGetPolicyTypes('custom_income_asset');

			// determine annuitants
			$view_data['annuitants_content'] = $this->RunDisplayAnnuitants($view_data);

			// get collateral content
			$view_data['collateral_content'] = $this->RunDisplayCollateralFormForLifeInsurance($view_data);

			// get beneficiaries content
			$view_data['beneficiaries_content'] = $this->RunDisplayBeneficiariesPeopleTrustsAndEntities($view_data);

			// get liquid_asset_type content
			$view_data['liquid_asset_types'] = EPlanModel::GetLiquidAssetTypes();
			$view_data['liquid_asset_type_content'] = $this->RunDisplayLiquidAssetTypeForm($view_data);

			return $this->IncludeMy('views/assets/asset_form_liquid_custom_income_asset.php', $view_data);
		}

	}

	protected function UpdateAssetAllocationForMoney($view_data){

		$structure_type = $view_data['asset_structure_code'];
		$type_code = $view_data['type_code'];

		/* ----- CASH ACCOUNT ----- */
		if ($structure_type == 'cash_account'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- RETAIL ACCOUNT ----- */
		if ($structure_type == 'retail_account'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- CASH VALUE LIFE INSURANCE ----- */
		if ($structure_type == 'cash_value_life_insurance'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		/* ----- DEFERRED ANNUITY ----- */
		if ($structure_type == 'deferred_annuity'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		/* ----- IMMEDIATE ANNUITY ----- */
		if ($structure_type == 'immediate_annuity'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		/* ----- ROTH IRA ----- */
		if ($structure_type == 'roth_ira'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- TRADITIONAL IRA ----- */
		if ($structure_type == 'traditional_ira'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- TRADITIONAL 401K ----- */
		if ($structure_type == 'traditional_401k'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- CUSTOM INCOME ASSET ----- */
		if ($structure_type == 'custom_income_asset'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		$money = $view_data['liquid_asset_type_data'][$type_code.'_money'];

		$percent_value = 0;
		if ($asset_value > 0){
			$percent_value = $money / $asset_value * 100;
		}

		$json_update_form['update_id'] = $type_code.'_percent_id';
		$json_update_form['update_html'] = number_format($percent_value, 2);
		$json_array[] = $json_update_form;

		return json_encode($json_array);
	}

	protected function UpdateAssetAllocationForPercent($view_data){

		$structure_type = $view_data['asset_structure_code'];
		$type_code = $view_data['type_code'];

		/* ----- CASH ACCOUNT ----- */
		if ($structure_type == 'cash_account'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- RETAIL ACCOUNT ----- */
		if ($structure_type == 'retail_account'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- CASH VALUE LIFE INSURANCE ----- */
		if ($structure_type == 'cash_value_life_insurance'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		/* ----- DEFERRED ANNUITY ----- */
		if ($structure_type == 'deferred_annuity'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		/* ----- IMMEDIATE ANNUITY ----- */
		if ($structure_type == 'immediate_annuity'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		/* ----- ROTH IRA ----- */
		if ($structure_type == 'roth_ira'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- TRADITIONAL IRA ----- */
		if ($structure_type == 'traditional_ira'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- TRADITIONAL 401K ----- */
		if ($structure_type == 'traditional_401k'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['fair_market_value']);
		}

		/* ----- CUSTOM INCOME ASSET ----- */
		if ($structure_type == 'custom_income_asset'){
			$asset_value = ereg_replace("[^0-9.]", "", $view_data['cash_value']);
		}

		$percent = $view_data['liquid_asset_type_data'][$type_code.'_percent'];

		$money_value = $asset_value * $percent / 100;

		$json_update_form['update_id'] = $type_code.'_money_id';
		$json_update_form['update_html'] = self::FormatMoney($money_value);
		$json_array[] = $json_update_form;

		return json_encode($json_array);
	}


	protected function RunSaveLiquidAssetForm($view_data){

		// ----- validation -----
		$view_data['_fields_with_errors'] = $this->DoValidationForLiquidAssetForm($view_data);

		$view_data['asset_classification_code'] = 'liquid';

		if (!empty($view_data['_fields_with_errors'])){

			$json_update_form['update_id'] = 'asset_form_liquid';
			$json_update_form['update_html'] = $this->RunDisplayLiquidAssetForm($view_data);
			$json_array[] = $json_update_form;

			return json_encode($json_array);
		}

		// ----- save data to DB -----

		if ($view_data['asset_structure_code'] == 'cash_account' && !isset($view_data['liquid_asset_type_data']['pre_tax'])
				&& !isset($view_data['liquid_asset_type_data']['post_tax']))
		{
			unset($view_data['liquid_asset_type_data']);
		}

		// check if the trust needs to be updated or saved
		if (!empty($view_data['asset_code'])){
			// update entity in DB
			if (!$this->DoUpdateAsset($view_data)){
				BourbonNotifier::AddUserErrorMessage($this->GetUserMessage('RunSaveLiquidAssetForm-cannot_update_asset'), $this->element_code . '_asset_overview_data');
			}
		}
		else {
			// add entity to DB
			if (!$this->DoAddAsset($view_data)){
				BourbonNotifier::AddUserErrorMessage($this->GetUserMessage('RunSaveLiquidAssetForm-cannot_add_asset'), $this->element_code . '_asset_overview_data');
			}
		}

		$json_update_form['update_id'] = 'eplan_assets_liquid';
		$json_update_form['update_html'] = $this->DoGetLiquidAssetOverviewData($view_data);
		$json_update_form['update_callback'] = BourbonUtilities::JSONScrollToID('eplan_assets_liquid');
		$json_array[] = $json_update_form;

		return json_encode($json_array);
	}

	protected function DoValidationForLiquidAssetForm($view_data){
		$_fields_with_errors = array();

		$form_field = 'asset_name';
		if (empty($view_data[$form_field])) $_fields_with_errors[] = $form_field;

		$form_field = 'asset_structure_code';
		if (empty($view_data[$form_field])) $_fields_with_errors[] = $form_field;

		// validate beneficiaries for asset structures
		$asset_structure_code = $view_data['asset_structure_code'];

		$asset_structures_with_beneficiaries = array('cash_value_life_insurance', 'deferred_annuity', 'immediate_annuity', 'roth_ira', 'traditional_ira', 'traditional_401k', 'custom_income_asset');

		if (in_array($asset_structure_code, $asset_structures_with_beneficiaries)){
			$this->DoValidateBeneficiariesForAsset($view_data, $_fields_with_errors);
		}

		// validate ownership data for ownership types
		$form_field = 'ownership_type';
		if (empty($view_data[$form_field])) $_fields_with_errors[] = $form_field;

		$form_field = 'ownership';

		$people = !empty($view_data[$form_field]['people_codes']) ? array_filter($view_data[$form_field]['people_codes']) : array();
		$trusts = !empty($view_data[$form_field]['trust_codes']) ? array_filter($view_data[$form_field]['trust_codes']) : array();
		$entities = !empty($view_data[$form_field]['entity_codes']) ? array_filter($view_data[$form_field]['entity_codes']) : array();

		if (empty($people) && empty($trusts) && empty($entities)){
			$_fields_with_errors[] = $form_field;
		}

		$ownership_data = @$view_data[$form_field];
		$ownership_type = @$view_data['ownership_type'];
		$ownership_percent = @$view_data['ownership']['ownership_percent'];

		if ($ownership_type == 'joint_with_right_of_survivorship'){

			$owner_1 = !empty($ownership_data['people_codes']) ? array_shift($ownership_data['people_codes']) : '';
			$owner_2 = !empty($ownership_data['people_codes']) ? array_shift($ownership_data['people_codes']) : '';

			if (empty($owner_1) || empty($owner_2) || $owner_1 == $owner_2){
				$_fields_with_errors[] = $form_field;
			}
		}

		if ($ownership_type == 'separate'){
			if (empty($ownership_data['people_codes'])) $_fields_with_errors[] = $form_field;
		}

		if ($ownership_type == 'trust'){
			if (empty($ownership_data['trust_codes'])) $_fields_with_errors[] = $form_field;
		}

		if ($ownership_type == 'entity'){
			if (empty($ownership_data['entity_codes'])) $_fields_with_errors[] = $form_field;
		}

		if ($ownership_type == 'tenancy_in_common' || $ownership_type == 'tenancy_in_partnership'){

			$total_percent = 0;

			if (!empty($ownership_data['people_codes'])){

				foreach ($ownership_data['people_codes'] as $display_order => $owner_code){
					$percent = @$ownership_percent[$owner_code];

					if (empty($percent)) continue;

					if (!is_numeric($percent)){
						$_fields_with_errors[] = $form_field;
						continue;
					}

					$total_percent += $percent;
				}
			}

			if (!empty($ownership_data['trust_codes'])){

				foreach ($ownership_data['trust_codes'] as $display_order => $owner_code){
					$percent = @$ownership_percent[$owner_code];

					if (empty($percent)) continue;

					if (!is_numeric($percent)){
						$_fields_with_errors[] = $form_field;
						continue;
					}

					$total_percent += $percent;
				}
			}

			if (!empty($ownership_data['entity_codes'])){

				foreach ($ownership_data['entity_codes'] as $display_order => $owner_code){
					$percent = @$ownership_percent[$owner_code];

					if (empty($percent)) continue;

					if (!is_numeric($percent)){
						$_fields_with_errors[] = $form_field;
						continue;
					}

					$total_percent += $percent;
				}
			}


			if ($total_percent != 100) $_fields_with_errors[] = $form_field;
		}

		// check collateral
		$form_field = 'collateral';
		if (!empty($view_data[$form_field])){
			$this->DoValidateCollateralForAsset($view_data, $_fields_with_errors);
		}

		$is_required = FALSE;

		if ($asset_structure_code == 'cash_account'){
			if (isset($view_data['liquid_asset_type_data']['pre_tax']) || isset($view_data['liquid_asset_type_data']['post_tax'])){
				$is_required = TRUE;
			}
		}
		elseif (isset($view_data['liquid_asset_type_data']['fixed']) || isset($view_data['liquid_asset_type_data']['variable'])
				|| isset($view_data['liquid_asset_type_data']['pre_tax']) || isset($view_data['liquid_asset_type_data']['post_tax'])){
			$is_required = TRUE;
		}

		// check for liquid asset types
		if ($is_required){

			$fixed_percent = 0;
			if (isset($view_data['liquid_asset_type_data']['fixed']) && !empty($view_data['liquid_asset_type_data']['fixed_percent'])){
				$fixed_percent = number_format($view_data['liquid_asset_type_data']['fixed_percent'], 2);
			}

			$variable_percent = 0;
			if (isset($view_data['liquid_asset_type_data']['variable']) && !empty($view_data['liquid_asset_type_data']['variable_percent'])){
				$variable_percent = number_format($view_data['liquid_asset_type_data']['variable_percent'], 2);
			}

			if ( ($fixed_percent + $variable_percent) <> 100){
				$_fields_with_errors[] = 'liquid_asset_type_data';
			}

			$pre_tax_percent = 0;
			if (isset($view_data['liquid_asset_type_data']['pre_tax']) && !empty($view_data['liquid_asset_type_data']['pre_tax_percent'])){
				$pre_tax_percent = number_format($view_data['liquid_asset_type_data']['pre_tax_percent'], 2);
			}

			$post_tax_percent = 0;
			if (isset($view_data['liquid_asset_type_data']['post_tax']) && !empty($view_data['liquid_asset_type_data']['post_tax_percent'])){
				$post_tax_percent = number_format($view_data['liquid_asset_type_data']['post_tax_percent'], 2);
			}

			if ( ($pre_tax_percent + $post_tax_percent) <> 100){
				$_fields_with_errors[] = 'liquid_asset_type_data';
			}
		}

		return $_fields_with_errors;
	}

	/* ---------------------------------------------------- */
	/* -------------------- ACTIONS ----------------------- */
	/* ---------------------------------------------------- */

}