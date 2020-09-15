<?php
	$inData = getRequestInfo();
	$servername = "localhost";
	$database = "eyeContacts";
	$username = "creator";
	$password = "plsdonthackmebro2";
	
	$searchResults = '{"results":[{';
	$numResults = 0;
	
	$conn = new mysqli($servername, $username, $password, $database);
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
	# select * from contacts where (first like '%abc%' or last like '%abc%')and userid = unameID;
		$sql = "SELECT (contactFirstName, contactLastName) 
		from contacts where contactFirstName LIKE '%" . $inData["search"] 
		. "%' or contactLastName LIKE '%" . $inData["search"] . " AND unameID=" . $inData["unameID"];
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
			while($row = $result->fetch_assoc())
			{
				if( $numResults > 0 )
				{
					$searchResults .= ",{";
				}
				$searchResults .= '"contactNumber":"' . $row["contactNumber"] . '","contactFirstName":"' . 
				$row["contactFirstName"] . '","contactLastName":"' . $row["contactLastName"] . '"}';
				$numResults++;
			}
			$searchResults .= '],';
			returnWithInfo( $searchResults, $searchCount );
		}
		else
		{
			returnWithError( "No Records Found" );
		}
		$conn->close();
	}	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"results":"[{}]","numContacts":0,"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults, $numResults )
	{
		$retValue = $searchResults . '"numResults":' . $numResults .'"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>