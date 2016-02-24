<?php

/**
 *	IP CALCULATION v1.0
 *	-------------------
 *	Author : <c.canuto>
 *	-------------------
 *	PHP 5.6
 *	-------------------
 *
 * INPUTS :
 * -------------------
 * Take as input an IPv4 address with CIDR format :
 * CIDR Format : 192.168.1.140/24
 *
 *	OUTPUTS :
 *	-------------------
 *	Output 
 * 
 */

/**
 * [splitCIDRaddr description]
 * @param  [string] $CIDRaddr [an IPv4 addr with CIDR format]
 * @return [type]             [description]
 */
function splitCIDRaddr($CIDRaddr){
	/**
	 * [$ipAddrArray description]
	 * @var [array]
	 * 
	 * $ipAddrArray[0]-> IPv4 Addr
	 * $ipAddrArray[1]-> CIDR Netmask
	 */
	$ipAddrArray = explode("/", $CIDRaddr);
	return $ipAddrArray;
}

/**
 * [generateNetmask description]
 * @param  [integer] $slashN [ex:  for /28 --> 28]
 * @return [type]            [ex:  for /28 --> 255.255.255.240]
 */
function generateNetmask($slashN) {
	$netmask = str_split(str_pad(str_pad('', $slashN, '1'), 32, '0'), 8);
	foreach ($netmask as &$element) $element = bindec($element);
	return join('.', $netmask);
}

/**
 * [addrPerSubnet description]
 * @param  [integer] $slashN [ex:  for /28 --> 28]
 * @return [type]            [ex:  for /28 --> 14 hosts]
 *
 *	NB : this function returns the number of addr per subnet, with broadcast addr and network addr
 *	If you want to know the usable addresses average, you will have to subtract 2 to the result of this function
 * 
 */
function addrPerSubnet($slashN) {
	if ($slashN > 32 || $slashN < 1) {
		return "Error : check the CIDR mask in input of the function addrPerSubnet(). Netmask is just improbable !";
	}
	else{
		$power = 32 - $slashN;
		// 2^n addresses
		$nbAddr = pow(2,$power)-2;
		return $nbAddr;
	}
}

function generateWildcardMask($slashN){
	$wildcardMaskBin = str_repeat("0", $slashN ) . str_repeat("1",  32-$slashN ); //inverse netmask binary
	$wildcardMask = bindec($wildcardMaskBin);
	$wildcardMask = long2ip($wildcardMask);
	return $wildcardMask;
}

function rangeExtremaAddr($CIDRaddr){

	$ipAddrArray = splitCIDRaddr($CIDRaddr);

	// Decimal ip address and netmask (ex : /28)
	$ipAddr = $ipAddrArray[0];
	$netmask = $ipAddrArray[1];

	// Binary netmask and binary inversed netmask
	$netmaskBin =str_repeat("1", $netmask ) . str_repeat("0", 32-$netmask );      //netmask binary
   $inversedNetmaskBin = str_repeat("0", $netmask ) . str_repeat("1",  32-$netmask ); //inverse netmask binary

   // Calculate Network Address from IP Addr / Mask
   $ipLong = ip2long( $ipAddr );
   $ipMaskLong = bindec( $netmaskBin );
   $inverseIpMaskLong = bindec( $inversedNetmaskBin );
   // Logiccal AND operator to get the network address
   $netWork = $ipLong & $ipMaskLong;

   // First Address of the range
   $start = $netWork + 1; //ignore Network Address
   $start = long2ip($start);

   // Broadcast Addr
   $broadcast = ($netWork | $inverseIpMaskLong); //Broadcast Address
   $broadcast = long2ip($broadcast);

   // Last Address of the range
   $end = ($netWork | $inverseIpMaskLong) - 1 ; //ignore Broadcast Address
   $end = long2ip($end);

   // Network Address
   $network = long2ip($netWork);

   return array( $start, $end, $broadcast, $network);
}

function rangeMinAddr($arrayExtrema){
	// Get the first address of a range
	$min = $arrayExtrema[0];
	return $min;
}

function rangeMaxAddr($arrayExtrema){
	// Get the first address of a range
	$max = $arrayExtrema[1];
	return $max;
}

function rangeBroadcastAddr($arrayExtrema){
	// Get the first address of a range
	$broadcast = $arrayExtrema[2];
	return $broadcast;
}

function rangeNetworkAddr($arrayExtrema){
	// Get the first address of a range
	$network = $arrayExtrema[3];
	return $network;
}





