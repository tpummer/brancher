<!DOCTYPE html>
<html>
<head>
  <title>Welcome Page</title>
</head>
<body>
  <h1>List of all Branches</h1>
  <ul>
  {foreach $branches as $branch}
    <li>{$branch} <a href="index.php?op=checkout&branch={$branch}">{if $branch eq $currentBranch}pull{else}checkout{/if}</a></li>
  {/foreach}
  </ul>
  <h1>Current Branch</h1>
  {$currentBranch}
  <p>{$lastCommitDate|date_format:"%Y-%m-%d %H:%M:%S"} - {$lastCommitAuthor}</p>
  <p>{$lastCommitId}</p>
  <p>{$lastCommitMessage}</p>
</body>
</html>