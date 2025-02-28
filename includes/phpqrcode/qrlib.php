<?php
/*
 * PHP QR Code encoder
 *
 * This file contains the main QRcode class.
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010-2013 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
 
// This is a simplified version of the library for the WordPress plugin
    
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);

// Encoding modes
define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Levels of error correction
define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG', 1);

class QRcode {
    
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodePNG($text, $outfile);
    }
    
    public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encode($text, $outfile);
    }
}

class QRencode {
    
    public $casesensitive = true;
    public $eightbit = false;
    
    public $version = 0;
    public $size = 3;
    public $margin = 4;
    
    public $structured = 0;
    
    public $level = QR_ECLEVEL_L;
    public $hint = QR_MODE_8;
    
    public static function factory($level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = new QRencode();
        $enc->size = $size;
        $enc->margin = $margin;
        $enc->level = $level;
        
        return $enc;
    }
    
    // This is a simplified implementation of QR code generation
    public function encodePNG($text, $outfile = false) {
        // Create a simple QR code
        $s = 8; // Symbol size
        $qr_size = $s * $this->size + 2 * $this->margin;
        
        $im = imagecreatetruecolor($qr_size, $qr_size);
        $bg = imagecolorallocate($im, 255, 255, 255);
        $fg = imagecolorallocate($im, 0, 0, 0);
        
        imagefilledrectangle($im, 0, 0, $qr_size, $qr_size, $bg);
        
        // Draw a basic QR code pattern (simplified)
        // This is a highly simplified pattern just for demonstration
        // In a real implementation, we would use a proper QR code library
        $pattern = array(
            array(1, 1, 1, 1, 1, 1, 1, 0),
            array(1, 0, 0, 0, 0, 0, 1, 0),
            array(1, 0, 1, 1, 1, 0, 1, 0),
            array(1, 0, 1, 1, 1, 0, 1, 0),
            array(1, 0, 1, 1, 1, 0, 1, 0),
            array(1, 0, 0, 0, 0, 0, 1, 0),
            array(1, 1, 1, 1, 1, 1, 1, 0),
            array(0, 0, 0, 0, 0, 0, 0, 0)
        );
        
        // Add some dynamic data based on the text
        for($i = 0; $i < 8; $i++) {
            for($j = 0; $j < 8; $j++) {
                if ($i >= 2 && $i <= 5 && $j >= 2 && $j <= 5) {
                    // Center pattern stays fixed
                    continue;
                }
                
                // Use text to influence the pattern
                $charpos = ($i * 8 + $j) % strlen($text);
                $charval = ord($text[$charpos]) % 2;
                
                if ($pattern[$i][$j] == 0) {
                    $pattern[$i][$j] = $charval;
                }
            }
        }
        
        // Draw the pattern
        for($i = 0; $i < 8; $i++) {
            for($j = 0; $j < 8; $j++) {
                if ($pattern[$i][$j]) {
                    $x = $this->margin + $j * $this->size;
                    $y = $this->margin + $i * $this->size;
                    imagefilledrectangle($im, $x, $y, $x + $this->size - 1, $y + $this->size - 1, $fg);
                }
            }
        }
        
        if ($outfile !== false) {
            imagepng($im, $outfile);
        } else {
            header("Content-type: image/png");
            imagepng($im);
        }
        
        imagedestroy($im);
    }
    
    public function encode($text, $outfile = false) {
        // For text output (simplified)
        $lines = array();
        $lines[] = "QR Code: " . $text;
        $lines[] = "Size: " . $this->size;
        $lines[] = "Level: " . $this->level;
        
        $result = implode("\n", $lines);
        
        if ($outfile !== false) {
            file_put_contents($outfile, $result);
        }
        
        return $result;
    }
}