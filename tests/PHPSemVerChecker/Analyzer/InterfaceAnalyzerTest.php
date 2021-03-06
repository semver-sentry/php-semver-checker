<?php

namespace PHPSemVerChecker\Test\Analyzer;

use PhpParser\Node\Stmt\Interface_;
use PHPSemVerChecker\Analyzer\InterfaceAnalyzer;
use PHPSemVerChecker\Registry\Registry;
use PHPSemVerChecker\SemanticVersioning\Level;
use PHPSemVerChecker\Test\Assertion\Assert;
use PHPSemVerChecker\Test\TestCase;

class InterfaceAnalyzerTest extends TestCase {
	public function testCompareSimilarInterface()
	{
		$before = new Registry();
		$after = new Registry();

		$interfaceBefore = new Interface_('tmp');
		$before->addInterface($interfaceBefore);

		$interfaceAfter = new Interface_('tmp');
		$after->addInterface($interfaceAfter);

		$analyzer = new InterfaceAnalyzer();
		$report = $analyzer->analyze($before, $after);

		Assert::assertNoDifference($report);
	}

	public function testInterfaceRemoved()
	{
		$before = new Registry();
		$after = new Registry();

		$before->addInterface(new Interface_('tmp'));

		$analyzer = new InterfaceAnalyzer();
		$report = $analyzer->analyze($before, $after);

		$context = 'interface';
		$expectedLevel = Level::MAJOR;
		Assert::assertDifference($report, $context, $expectedLevel);
		$this->assertSame('V033', $report[$context][$expectedLevel][0]->getCode());
		$this->assertSame('Interface was removed.', $report[$context][$expectedLevel][0]->getReason());
		$this->assertSame('tmp', $report[$context][$expectedLevel][0]->getTarget());
	}

	public function testInterfaceAdded()
	{
		$before = new Registry();
		$after = new Registry();

		$after->addInterface(new Interface_('tmp'));

		$analyzer = new InterfaceAnalyzer();
		$report = $analyzer->analyze($before, $after);

		$context = 'interface';
		$expectedLevel = Level::MINOR;
		Assert::assertDifference($report, $context, $expectedLevel);
		$this->assertSame('V032', $report[$context][$expectedLevel][0]->getCode());
		$this->assertSame('Interface was added.', $report[$context][$expectedLevel][0]->getReason());
		$this->assertSame('tmp', $report[$context][$expectedLevel][0]->getTarget());
	}
}
