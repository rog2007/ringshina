<?php
  $tit="Индекс нагрузки";
  $desk="Индекс нагрузки для автомобильных шин";
  $kw="Индекс нагрузки";
  $res=mysql_query("select txt from pages where pg='".$page."'");
  if($rs=mysql_fetch_object($res))
  $str.=$rs->txt;
  //$h1="Индекс нагрузки";
  /*$str="<h1>".$h1."</h1>
  <div class=\"statbl\">
  <table class=\"bord\">
        <tr width=\"800\" height=\"25\">
            <td width=\"400\" class=\"bord\" style=\"background-color: rgb(198, 200, 193);\" align=\"center\"><strong>LI</strong></td>
            <td width=\"400\" class=\"bord\" style=\"background-color: rgb(198, 200, 193);\" align=\"center\"><strong>кг</strong></td>
        </tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>70</strong></td><td class=\"bord\" align=\"center\"><strong>335</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>71</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>345</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>72</strong></td><td class=\"bord\" align=\"center\"><strong>355</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>73</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>365</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>74</strong></td><td class=\"bord\" align=\"center\"><strong>375</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>75</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>387</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>76</strong></td><td class=\"bord\" align=\"center\"><strong>400</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>77</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>412</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>78</strong></td><td class=\"bord\" align=\"center\"><strong>425</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>79</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>437</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>80</strong></td><td class=\"bord\" align=\"center\"><strong>450</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>81</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>462</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>82</strong></td><td class=\"bord\" align=\"center\"><strong>475</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>83</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>487</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>84</strong></td><td class=\"bord\" align=\"center\"><strong>500</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>85</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>515</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>86</strong></td><td class=\"bord\" align=\"center\"><strong>530</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>87</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>545</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>88</strong></td><td class=\"bord\" align=\"center\"><strong>>560</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>89</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>580</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>90</strong></td><td class=\"bord\" align=\"center\"><strong>>600</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>91</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>615</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>92</strong></td><td class=\"bord\" align=\"center\"><strong>630</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>93</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>650</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>94</strong></td><td class=\"bord\" align=\"center\"><strong>670</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>95</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>690</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>96</strong></td><td class=\"bord\" align=\"center\"><strong>710</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>97</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>730</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>98</strong></td><td class=\"bord\" align=\"center\"><strong>750</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>99</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>775</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>100</strong></td><td class=\"bord\" align=\"center\"><strong>800</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>101</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>825</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>102</strong></td><td class=\"bord\" align=\"center\"><strong>850</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>103</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>875</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>104</strong></td><td class=\"bord\" align=\"center\"><strong>900</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>105</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>925</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>106</strong></td><td class=\"bord\" align=\"center\"><strong>950</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>107</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>975</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>108</strong></td><td class=\"bord\" align=\"center\"><strong>1000</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>109</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>1030</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>110</strong></td><td class=\"bord\" align=\"center\"><strong>1060</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>111</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>1090</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>112</strong></td><td class=\"bord\" align=\"center\"><strong>1120</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>113</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>1150</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>114</strong></td><td class=\"bord\" align=\"center\"><strong>1180</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>115</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>1215</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>116</strong></td><td class=\"bord\" align=\"center\"><strong>1250</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>117</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>1285</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>118</strong></td><td class=\"bord\" align=\"center\"><strong>1320</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>119</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>1360</strong></td></tr>

  </table>

  </div>";*/
?>