<?php

require __DIR__ . '/vendor/autoload.php';

# set up logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('brancher');
$log->pushHandler(new StreamHandler(__DIR__ . '/brancher.log', Logger::DEBUG));

# load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$gitdir = $_ENV['GITDIR'];
$log->debug('gitdir: ' . $gitdir);

# set up smarty
$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__ . '/templates');
$smarty->setCompileDir(__DIR__ . '/templates_c');

# read git status
$git = new CzProject\GitPhp\Git(new CzProject\GitPhp\Runners\OldGitRunner());
$repo = $git->open($gitdir . '/.git');

$currentBranch = $repo->getCurrentBranchName();
$branches = $repo->getRemoteBranches();
// remove origin/HEAD entry
array_shift($branches);
//foreach branch remove origin/ prefix
foreach($branches as $key => $branch) {
    $branches[$key] = str_replace('origin/', '', $branch);
}


if(isset($_GET['op']) == 'checkout' && isset($_GET['branch']) && in_array($_GET['branch'], $branches))  {
    $branch = $_GET['branch'];
    $log->debug('checkout branch: ' . $branch);
    $repo->checkout($branch);
    $repo->pull();
    $currentBranch = $repo->getCurrentBranchName();
}


$lastCommit = $repo->getLastCommit();
#uncomment to view compltee commit object
#var_dump($lastCommit);
$lastCommitId = $lastCommit->getId();
$lastCommitAuthor = $lastCommit->getAuthorName();
$lastCommitDate = $lastCommit->getAuthorDate();
$lastCommitMessage = $lastCommit->getSubject();

# set up smarty variables
$smarty->assign('currentBranch', $currentBranch);
$smarty->assign('branches', $branches);
$smarty->assign('lastCommitId', $lastCommitId);
$smarty->assign('lastCommitAuthor', $lastCommitAuthor);
$smarty->assign('lastCommitDate', $lastCommitDate);
$smarty->assign('lastCommitMessage', $lastCommitMessage);

# display template
$smarty->display('index.tpl');