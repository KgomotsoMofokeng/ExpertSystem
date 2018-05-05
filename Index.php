<!DOCTYPE HTML>
<html>
	<head>
		<title>ExpertSysytem</title>
		<link rel="stylesheet" href="ESystem.css">
	</head>
		<body>
		<div id="main">
			<div id="header">
				<div>
					<h1><a href="https://en.wikipedia.org/wiki/Expert_system">Expert System</a></h1>
				</div>
				<div>
					<p></p>
				</div>		
			</div>
			<div id="navbar">
			</div>
			<div id="dispbox">
				<div id="addfile">
					 <form action="ExpertSystem.php" method="post">
						 <label for="path"><tag>File Path*</tag></label>
						<input type="text" id="path" value="" name="filepath" placeholder="e.g ~/Desktop/tmp/instr.txt">
						<br/><label for="path"><tag>Alternate Facts</tag></label>
						<input type="text" id="PrimaryFacts" value="" name="facts" placeholder="e.g AFGH">
						<br/><input type="submit" name="submit" value="submit">
						<br/>Scroll down for a quick and summarised userguide.
					</form>
				</div>
				<div id="screen">
					<?PHP
						include("expert.php");
						session_start();
						if ($_SESSION["ERROR"])
							print($_SESSION["ERROR"]);
						else
						{
							print($_SESSION["toValidate"]."<br/>");
							if ($_SESSION[$ruleset]) 
							{
								print ("Ruleset :<br/><br/>");
								foreach ($_SESSION[$ruleset] as $rule)
									print ($rule."<br/>");
								echo "<br/>According to the Rulset above and Facts provided the conclusion is as follows:<br/><br/>";
								if (!$_SESSION["dictions"])
								{
									if($_SESSION["invalid"])
										echo $_SESSION["invalid"];
									else
									{	
										theEnd();
										echo "<br/>";
										qry();
									}
								}
								else 
								{
									print ("ERROR! Contradictions found.<br/><br/>");
									foreach ($_SESSION["dictions"] as $elem)
										print($elem."<br />");
								}
							}
						}
						session_destroy();
					?>
				</div>
			</div>
			<div id="foot">
				<h2>How it works!</h2>
				<p>By default, all facts are false, and can only be made true by the initial facts statement,
					or by application of a rule. A fact can only be undetermined if the ruleset is ambiguous,
					for example if I say "A is true, also if A then B or C", then B and C are undetermined.
					If there is an error in the input, for example, a contradiction in the facts, or a syntax
					error, the program will inform the user of the problem.
				</p>
				<h3> The "File Path" field</h3>
				<p>The "File Path" field is compulsory: enter the required instruction/fact file location togather with its name
					and click the submit button</p>
				<h3> The "Alternate Facts" field</h3>
				<p>If you would like to use the same ruleset with new facts, enter them in the "Alternate Facts" field the new 
					facts will be given priority over the ones provide with the ruleset in the input file.</p>
				<p><b>NB!</b>...For a more comprihansive document please refer to the subject, expertsystem.pdf</p>	
			</div>
		</div>
		<script></script>
	</body>
</html>
