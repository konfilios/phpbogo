<?php
namespace Bogo\Util;

/**
 * Internet utility functions.
 * 
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Inet
{
	/**
	 * Tests if a test IPv4 address matches the an IPv4 range given in CIDR format.
	 *
	 * @link http://stackoverflow.com/a/594134/1289270
	 * @param string $testIpHuman E.g. "192.168.254.23"
	 * @param string $targetCidr E.g. "140.25.16.0/20"
	 * @return boolean
	 */
	static public function matchesCidrIp4($testIpHuman, $targetCidr)
	{
		// Extract subnet ip from number of digits for mask
		list ($targetSubnetHuman, $targetMaskBits) = explode('/', $targetCidr);

		// Convert human representations to longs
		$testLongIp = ip2long($testIpHuman);
		$targetLongSubnet = ip2long($targetSubnetHuman);
		$targetLongMask = -1 << (32 - $targetMaskBits);

		// Get properly padded, masked representations of test and target ip addresses
		$maskedTestLongIp = ($testLongIp & $targetLongMask);
		$maskedTargetLongSubnet = ($targetLongSubnet & $targetLongMask);

		// Compare
		return ($maskedTestLongIp == $maskedTargetLongSubnet);
	}

	/**
	 * Tests if a test IPv6 address matches the an IPv6 range given in CIDR format.
	 *
	 * @link http://stackoverflow.com/a/7951507/1289270
	 * @param string $testIpHuman E.g. "21DA:00D3:0001:2F3B:02AC:00FF:FE28:9C5A"
	 * @param string $targetCidr E.g. "21DA:00D3:0000:2F3B::/64"
	 * @return boolean
	 */
	static public function matchesCidrIp6($testIpHuman, $targetCidr)
	{
		// Extract subnet ip from number of digits for mask
		list($targetSubnetHuman, $targetMaskBitsDecString) = explode('/',$targetCidr);

		// Convert human representations to equally padded binary strings
		$testIpBinString = self::ipHumanToIpBinString($testIpHuman);
		$targetSubnetBinString = self::ipHumanToIpBinString($targetSubnetHuman);

		// Test only first $targetMaskBitsDecString bits
		$maskedTestIpBinString = substr($testIpBinString, 0, $targetMaskBitsDecString);
		$maskedTargetSubnetBinString = substr($targetSubnetBinString, 0, $targetMaskBitsDecString);

		return ($maskedTestIpBinString === $maskedTargetSubnetBinString);
	}

	/**
	 * Converts a human readable IP to a binary string representation using inet_pton.
	 *
	 * E.g.: IPv6 Address "21DA:00D3:0001:2F3B:02AC:00FF:FE28:9C5A" yields the binary string
	 * "00100001110110100000000011010011000000000000000100101111001110110000001010101100000000001111111111111110001010001001110001011010"
	 *
	 * @link http://stackoverflow.com/a/7951507/1289270
	 * @param string $ipHuman E.g. "21DA:00D3:0001:2F3B:02AC:00FF:FE28:9C5A"
	 * @return string
	 */
	static public function ipHumanToIpBinString($ipHuman)
	{
		// Convert human readable ip into a 128-bit (16-byte) binary (packed) value
		$packedIp = inet_pton($ipHuman);

		// Unpack into a string of 16 characters
		$unpackedIp = unpack('A16', $packedIp);

		// Split characters into an array of 16 character items
		$ipHexChars = str_split($unpackedIp[1]);

		$ipBinString = '';
		foreach ($ipHexChars as $ipHexChar) {
			// Get ordinal (decimal) value of ASCII character (ord)
			// transform it into a string of binary digits (decbin)
			// and pad it to the left with zeroes to make sure it always takes up 8 digits (str_pad)
		   $ipBinString .= str_pad(decbin(ord($ipHexChar)), 8, '0', STR_PAD_LEFT);
		}

		return $ipBinString;
	}
}
